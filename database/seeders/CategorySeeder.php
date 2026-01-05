<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main categories (parent categories)
        $categories = [
            [
                'name' => 'Fiksi',
                'description' => 'Buku-buku fiksi termasuk novel, cerpen, dan cerita fantasi',
                'children' => [
                    ['name' => 'Novel', 'description' => 'Novel fiksi berbagai genre'],
                    ['name' => 'Cerpen', 'description' => 'Kumpulan cerita pendek'],
                    ['name' => 'Fantasi', 'description' => 'Buku fiksi fantasi dan sihir'],
                    ['name' => 'Misteri', 'description' => 'Buku fiksi misteri dan detektif'],
                    ['name' => 'Romansa', 'description' => 'Novel romantis'],
                ],
            ],
            [
                'name' => 'Non-Fiksi',
                'description' => 'Buku-buku non-fiksi berdasarkan fakta',
                'children' => [
                    ['name' => 'Biografi', 'description' => 'Biografi dan autobiografi'],
                    ['name' => 'Sejarah', 'description' => 'Buku sejarah dan dokumenter'],
                    ['name' => 'Sains Populer', 'description' => 'Sains untuk pembaca umum'],
                    ['name' => 'Self-Help', 'description' => 'Pengembangan diri dan motivasi'],
                ],
            ],
            [
                'name' => 'Teknologi & Komputer',
                'description' => 'Buku-buku tentang teknologi, pemrograman, dan komputer',
                'children' => [
                    ['name' => 'Pemrograman', 'description' => 'Buku tentang pemrograman dan coding'],
                    ['name' => 'Database', 'description' => 'Buku tentang database dan data management'],
                    ['name' => 'Jaringan', 'description' => 'Buku tentang jaringan komputer'],
                    ['name' => 'Keamanan Siber', 'description' => 'Buku tentang keamanan informasi'],
                    ['name' => 'Kecerdasan Buatan', 'description' => 'Buku tentang AI dan machine learning'],
                ],
            ],
            [
                'name' => 'Bisnis & Ekonomi',
                'description' => 'Buku-buku tentang bisnis, ekonomi, dan manajemen',
                'children' => [
                    ['name' => 'Manajemen', 'description' => 'Buku tentang manajemen bisnis'],
                    ['name' => 'Keuangan', 'description' => 'Buku tentang keuangan dan investasi'],
                    ['name' => 'Pemasaran', 'description' => 'Buku tentang marketing dan branding'],
                    ['name' => 'Kewirausahaan', 'description' => 'Buku tentang entrepreneurship'],
                ],
            ],
            [
                'name' => 'Pendidikan',
                'description' => 'Buku-buku pendidikan dan referensi akademik',
                'children' => [
                    ['name' => 'Matematika', 'description' => 'Buku matematika'],
                    ['name' => 'Fisika', 'description' => 'Buku fisika'],
                    ['name' => 'Kimia', 'description' => 'Buku kimia'],
                    ['name' => 'Biologi', 'description' => 'Buku biologi'],
                    ['name' => 'Bahasa', 'description' => 'Buku pembelajaran bahasa'],
                ],
            ],
            [
                'name' => 'Seni & Budaya',
                'description' => 'Buku-buku tentang seni, musik, dan budaya',
                'children' => [
                    ['name' => 'Seni Rupa', 'description' => 'Buku tentang seni lukis dan visual'],
                    ['name' => 'Musik', 'description' => 'Buku tentang musik dan musisi'],
                    ['name' => 'Film', 'description' => 'Buku tentang film dan sinema'],
                    ['name' => 'Fotografi', 'description' => 'Buku tentang fotografi'],
                ],
            ],
            [
                'name' => 'Agama & Spiritualitas',
                'description' => 'Buku-buku keagamaan dan spiritual',
                'children' => [
                    ['name' => 'Islam', 'description' => 'Buku-buku Islam'],
                    ['name' => 'Kristen', 'description' => 'Buku-buku Kristen'],
                    ['name' => 'Filsafat', 'description' => 'Buku filsafat dan pemikiran'],
                ],
            ],
            [
                'name' => 'Anak-anak',
                'description' => 'Buku-buku untuk anak-anak',
                'children' => [
                    ['name' => 'Cerita Anak', 'description' => 'Cerita untuk anak-anak'],
                    ['name' => 'Komik Anak', 'description' => 'Komik untuk anak-anak'],
                    ['name' => 'Edukasi Anak', 'description' => 'Buku pendidikan untuk anak'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $parent = Category::create($categoryData);
            
            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                Category::create($childData);
            }
        }
    }
}
