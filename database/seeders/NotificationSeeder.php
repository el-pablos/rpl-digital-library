<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = User::role('member')->get();
        $librarians = User::role('librarian')->get();
        $admins = User::role('admin')->get();

        if ($members->isEmpty()) {
            $this->command->warn('Skipping NotificationSeeder: No members found.');
            return;
        }

        // Notifications for members
        foreach ($members->take(5) as $member) {
            // Loan approved notification (read)
            Notification::create([
                'user_id' => $member->id,
                'type' => Notification::TYPE_LOAN_APPROVED,
                'message' => 'Peminjaman buku Anda telah disetujui. Silakan ambil buku di perpustakaan dalam waktu 3 hari.',
                'is_read' => true,
                'created_at' => now()->subDays(3),
            ]);

            // Due reminder notification (unread)
            Notification::create([
                'user_id' => $member->id,
                'type' => Notification::TYPE_DUE_REMINDER,
                'message' => 'Buku yang Anda pinjam akan jatuh tempo dalam 2 hari. Silakan kembalikan atau perpanjang.',
                'is_read' => false,
                'created_at' => now()->subDay(),
            ]);
        }

        // Overdue notifications
        foreach ($members->take(2) as $member) {
            Notification::create([
                'user_id' => $member->id,
                'type' => Notification::TYPE_OVERDUE,
                'message' => 'Buku yang Anda pinjam sudah melewati batas waktu pengembalian. Denda akan dikenakan.',
                'is_read' => false,
                'created_at' => now()->subHours(12),
            ]);
        }

        // Fine notification
        Notification::create([
            'user_id' => $members->first()->id,
            'type' => Notification::TYPE_FINE_CREATED,
            'message' => 'Anda memiliki denda sebesar Rp 5.000 yang harus dibayar.',
            'is_read' => false,
            'created_at' => now()->subHours(6),
        ]);

        // Return confirmed notification
        Notification::create([
            'user_id' => $members->skip(1)->first()->id,
            'type' => Notification::TYPE_RETURN_CONFIRMED,
            'message' => 'Pengembalian buku Anda telah dikonfirmasi. Terima kasih telah mengembalikan tepat waktu.',
            'is_read' => true,
            'created_at' => now()->subDays(2),
        ]);

        // Review approved notification
        Notification::create([
            'user_id' => $members->skip(2)->first()->id ?? $members->first()->id,
            'type' => Notification::TYPE_REVIEW_APPROVED,
            'message' => 'Review Anda telah disetujui dan sekarang dapat dilihat oleh pengguna lain.',
            'is_read' => false,
            'created_at' => now()->subHours(4),
        ]);
    }
}
