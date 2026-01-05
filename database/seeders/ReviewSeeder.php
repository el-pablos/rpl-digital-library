<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\ReviewVote;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = User::role('member')->where('status', 'active')->get();
        $books = Book::all();

        if ($members->isEmpty() || $books->isEmpty()) {
            $this->command->warn('Skipping ReviewSeeder: Required data not found.');
            return;
        }

        $reviewContents = [
            5 => [
                'Buku yang sangat bagus! Sangat direkomendasikan untuk dibaca.',
                'Luar biasa! Salah satu buku terbaik yang pernah saya baca.',
                'Penjelasannya sangat jelas dan mudah dipahami. Recommended!',
                'Buku wajib baca untuk semua orang yang tertarik dengan topik ini.',
            ],
            4 => [
                'Buku yang bagus, meskipun ada beberapa bagian yang kurang jelas.',
                'Secara keseluruhan bagus, tapi butuh update untuk edisi terbaru.',
                'Informatif dan menarik, layak dibaca.',
                'Baik untuk pemula, tapi kurang mendalam untuk yang sudah expert.',
            ],
            3 => [
                'Cukup bagus, tapi masih banyak yang bisa diperbaiki.',
                'Standar, tidak buruk tapi juga tidak istimewa.',
                'Isi buku lumayan, tapi gaya penulisannya membosankan.',
            ],
            2 => [
                'Kurang memuaskan, banyak informasi yang sudah outdated.',
                'Sulit dipahami dan tidak terstruktur dengan baik.',
            ],
            1 => [
                'Sangat mengecewakan. Tidak sesuai ekspektasi.',
            ],
        ];

        // Create approved reviews
        foreach ($books->take(15) as $book) {
            // 2-4 reviews per book
            $numReviews = rand(2, 4);
            $usedMembers = [];

            for ($i = 0; $i < $numReviews; $i++) {
                // Ensure unique user per book
                do {
                    $member = $members->random();
                } while (in_array($member->id, $usedMembers) && count($usedMembers) < $members->count());

                if (in_array($member->id, $usedMembers)) {
                    continue;
                }
                $usedMembers[] = $member->id;

                // Weight towards higher ratings
                $rating = $this->getWeightedRating();
                $reviewText = $reviewContents[$rating][array_rand($reviewContents[$rating])];

                $review = Review::create([
                    'user_id' => $member->id,
                    'book_id' => $book->id,
                    'rating' => $rating,
                    'review_text' => $reviewText,
                    'status' => Review::STATUS_APPROVED,
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);

                // Add some votes to approved reviews
                $this->addVotesToReview($review, $members, $usedMembers);
            }
        }

        // Create pending reviews (waiting for moderation)
        for ($i = 0; $i < 3; $i++) {
            $book = $books->random();
            $member = $members->random();

            // Check if this user already reviewed this book
            $exists = Review::where('user_id', $member->id)
                ->where('book_id', $book->id)
                ->exists();

            if (!$exists) {
                $rating = $this->getWeightedRating();
                Review::create([
                    'user_id' => $member->id,
                    'book_id' => $book->id,
                    'rating' => $rating,
                    'review_text' => $reviewContents[$rating][array_rand($reviewContents[$rating])],
                    'status' => Review::STATUS_PENDING,
                    'created_at' => now()->subDays(rand(0, 2)),
                ]);
            }
        }

        // Create rejected review (for testing)
        // Find a unique user-book combination that doesn't exist yet
        foreach ($members as $member) {
            foreach ($books->skip(15) as $book) {
                $exists = Review::where('user_id', $member->id)
                    ->where('book_id', $book->id)
                    ->exists();

                if (!$exists) {
                    Review::create([
                        'user_id' => $member->id,
                        'book_id' => $book->id,
                        'rating' => 1,
                        'review_text' => 'Review ini ditolak karena melanggar ketentuan.',
                        'status' => Review::STATUS_REJECTED,
                        'created_at' => now()->subDays(5),
                    ]);
                    break 2; // Exit both loops
                }
            }
        }
    }

    /**
     * Get weighted rating (more 4-5 stars than 1-2).
     */
    private function getWeightedRating(): int
    {
        $weights = [
            5 => 35,
            4 => 40,
            3 => 15,
            2 => 7,
            1 => 3,
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $rating => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $rating;
            }
        }

        return 4;
    }

    /**
     * Add votes to a review.
     */
    private function addVotesToReview(Review $review, $members, array $excludeMembers): void
    {
        // 0-5 votes per review
        $numVotes = rand(0, 5);

        if ($numVotes === 0) {
            return;
        }

        $availableVoters = $members->filter(function ($member) use ($excludeMembers, $review) {
            return !in_array($member->id, $excludeMembers) && $member->id !== $review->user_id;
        });

        if ($availableVoters->isEmpty()) {
            return;
        }

        $voters = $availableVoters->random(min($numVotes, $availableVoters->count()));

        // Handle single voter case
        if (!is_iterable($voters) || $voters instanceof User) {
            $voters = collect([$voters]);
        }

        foreach ($voters as $voter) {
            // 70% helpful, 30% not_helpful
            $voteType = rand(1, 100) <= 70 ? 'helpful' : 'not_helpful';

            ReviewVote::create([
                'review_id' => $review->id,
                'user_id' => $voter->id,
                'vote_type' => $voteType,
            ]);
        }

        // Update vote counts on review
        $review->update([
            'helpful_count' => ReviewVote::where('review_id', $review->id)->where('vote_type', 'helpful')->count(),
            'not_helpful_count' => ReviewVote::where('review_id', $review->id)->where('vote_type', 'not_helpful')->count(),
        ]);
    }
}
