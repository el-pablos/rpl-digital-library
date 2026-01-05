<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            // Novel (Fiksi > Novel)
            [
                'isbn' => '978-602-291-123-4',
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'publication_year' => 2005,
                'category' => 'Novel',
                'description' => 'Novel yang menceritakan tentang perjuangan anak-anak di Belitung untuk mendapatkan pendidikan.',
                'total_copies' => 5,
                'available_copies' => 3,
                'language' => 'Indonesian',
                'pages' => 529,
            ],
            [
                'isbn' => '978-602-291-124-5',
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'publisher' => 'Hasta Mitra',
                'publication_year' => 1980,
                'category' => 'Novel',
                'description' => 'Novel pertama dari Tetralogi Buru yang mengisahkan kehidupan di era kolonial.',
                'total_copies' => 4,
                'available_copies' => 2,
                'language' => 'Indonesian',
                'pages' => 535,
            ],
            [
                'isbn' => '978-602-291-125-6',
                'title' => 'Perahu Kertas',
                'author' => 'Dee Lestari',
                'publisher' => 'Bentang Pustaka',
                'publication_year' => 2009,
                'category' => 'Novel',
                'description' => 'Novel romantis tentang dua remaja dengan passion berbeda yang bertemu di kampus.',
                'total_copies' => 3,
                'available_copies' => 3,
                'language' => 'Indonesian',
                'pages' => 444,
            ],
            
            // Pemrograman (Teknologi > Pemrograman)
            [
                'isbn' => '978-0-13-468599-1',
                'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'author' => 'Robert C. Martin',
                'publisher' => 'Prentice Hall',
                'publication_year' => 2008,
                'category' => 'Pemrograman',
                'description' => 'Buku panduan menulis kode yang bersih dan mudah dimaintain.',
                'total_copies' => 3,
                'available_copies' => 2,
                'language' => 'English',
                'pages' => 464,
            ],
            [
                'isbn' => '978-0-596-51774-8',
                'title' => 'JavaScript: The Good Parts',
                'author' => 'Douglas Crockford',
                'publisher' => "O'Reilly Media",
                'publication_year' => 2008,
                'category' => 'Pemrograman',
                'description' => 'Buku tentang bagian-bagian terbaik dari JavaScript.',
                'total_copies' => 2,
                'available_copies' => 1,
                'language' => 'English',
                'pages' => 176,
            ],
            [
                'isbn' => '978-1-491-95039-0',
                'title' => 'Learning PHP, MySQL & JavaScript',
                'author' => 'Robin Nixon',
                'publisher' => "O'Reilly Media",
                'publication_year' => 2018,
                'category' => 'Pemrograman',
                'description' => 'Panduan lengkap untuk web development dengan PHP, MySQL, dan JavaScript.',
                'total_copies' => 4,
                'available_copies' => 4,
                'language' => 'English',
                'pages' => 800,
            ],
            [
                'isbn' => '978-1-491-94399-5',
                'title' => 'Laravel: Up & Running',
                'author' => 'Matt Stauffer',
                'publisher' => "O'Reilly Media",
                'publication_year' => 2019,
                'category' => 'Pemrograman',
                'description' => 'Panduan komprehensif untuk framework Laravel.',
                'total_copies' => 3,
                'available_copies' => 2,
                'language' => 'English',
                'pages' => 556,
            ],
            
            // Database
            [
                'isbn' => '978-0-321-88457-1',
                'title' => 'Database Design for Mere Mortals',
                'author' => 'Michael J. Hernandez',
                'publisher' => 'Addison-Wesley',
                'publication_year' => 2013,
                'category' => 'Database',
                'description' => 'Panduan praktis untuk desain database relasional.',
                'total_copies' => 2,
                'available_copies' => 2,
                'language' => 'English',
                'pages' => 672,
            ],
            
            // Kecerdasan Buatan
            [
                'isbn' => '978-0-13-461099-7',
                'title' => 'Artificial Intelligence: A Modern Approach',
                'author' => 'Stuart Russell, Peter Norvig',
                'publisher' => 'Pearson',
                'publication_year' => 2020,
                'category' => 'Kecerdasan Buatan',
                'description' => 'Buku teks komprehensif tentang kecerdasan buatan.',
                'total_copies' => 3,
                'available_copies' => 1,
                'language' => 'English',
                'pages' => 1136,
            ],
            [
                'isbn' => '978-1-492-03224-3',
                'title' => 'Hands-On Machine Learning',
                'author' => 'Aurélien Géron',
                'publisher' => "O'Reilly Media",
                'publication_year' => 2019,
                'category' => 'Kecerdasan Buatan',
                'description' => 'Panduan praktis machine learning dengan Scikit-Learn dan TensorFlow.',
                'total_copies' => 2,
                'available_copies' => 2,
                'language' => 'English',
                'pages' => 856,
            ],
            
            // Self-Help
            [
                'isbn' => '978-602-291-126-7',
                'title' => 'Atomic Habits (Edisi Indonesia)',
                'author' => 'James Clear',
                'publisher' => 'Gramedia Pustaka Utama',
                'publication_year' => 2019,
                'category' => 'Self-Help',
                'description' => 'Cara mudah dan terbukti untuk membangun kebiasaan baik.',
                'total_copies' => 5,
                'available_copies' => 3,
                'language' => 'Indonesian',
                'pages' => 352,
            ],
            [
                'isbn' => '978-602-291-127-8',
                'title' => 'Filosofi Teras',
                'author' => 'Henry Manampiring',
                'publisher' => 'Kompas',
                'publication_year' => 2018,
                'category' => 'Self-Help',
                'description' => 'Filsafat Yunani-Romawi untuk mental tangguh di masa modern.',
                'total_copies' => 4,
                'available_copies' => 2,
                'language' => 'Indonesian',
                'pages' => 346,
            ],
            
            // Sejarah
            [
                'isbn' => '978-602-291-128-9',
                'title' => 'Sapiens: Riwayat Singkat Umat Manusia',
                'author' => 'Yuval Noah Harari',
                'publisher' => 'Kepustakaan Populer Gramedia',
                'publication_year' => 2017,
                'category' => 'Sejarah',
                'description' => 'Sejarah umat manusia dari zaman purba hingga modern.',
                'total_copies' => 3,
                'available_copies' => 2,
                'language' => 'Indonesian',
                'pages' => 564,
            ],
            
            // Manajemen
            [
                'isbn' => '978-602-291-129-0',
                'title' => 'The Lean Startup',
                'author' => 'Eric Ries',
                'publisher' => 'Bentang Pustaka',
                'publication_year' => 2011,
                'category' => 'Manajemen',
                'description' => 'Cara membangun startup dengan efisien.',
                'total_copies' => 3,
                'available_copies' => 3,
                'language' => 'Indonesian',
                'pages' => 336,
            ],
            [
                'isbn' => '978-602-291-130-6',
                'title' => 'Good to Great',
                'author' => 'Jim Collins',
                'publisher' => 'Gramedia Pustaka Utama',
                'publication_year' => 2001,
                'category' => 'Manajemen',
                'description' => 'Mengapa beberapa perusahaan bisa lompat ke level hebat.',
                'total_copies' => 2,
                'available_copies' => 1,
                'language' => 'Indonesian',
                'pages' => 400,
            ],
            
            // Matematika
            [
                'isbn' => '978-602-291-131-3',
                'title' => 'Kalkulus Jilid 1',
                'author' => 'Purcell, Varberg, Rigdon',
                'publisher' => 'Erlangga',
                'publication_year' => 2010,
                'category' => 'Matematika',
                'description' => 'Buku teks kalkulus untuk mahasiswa teknik dan sains.',
                'total_copies' => 10,
                'available_copies' => 7,
                'language' => 'Indonesian',
                'pages' => 500,
            ],
            [
                'isbn' => '978-602-291-132-0',
                'title' => 'Aljabar Linear Elementer',
                'author' => 'Howard Anton',
                'publisher' => 'Erlangga',
                'publication_year' => 2014,
                'category' => 'Matematika',
                'description' => 'Buku teks aljabar linear untuk mahasiswa.',
                'total_copies' => 8,
                'available_copies' => 6,
                'language' => 'Indonesian',
                'pages' => 624,
            ],
            
            // Fisika
            [
                'isbn' => '978-602-291-133-7',
                'title' => 'Fisika Dasar Jilid 1',
                'author' => 'Halliday, Resnick, Walker',
                'publisher' => 'Erlangga',
                'publication_year' => 2013,
                'category' => 'Fisika',
                'description' => 'Buku teks fisika dasar untuk mahasiswa.',
                'total_copies' => 10,
                'available_copies' => 8,
                'language' => 'Indonesian',
                'pages' => 600,
            ],
            
            // Fantasi
            [
                'isbn' => '978-602-291-134-4',
                'title' => 'Harry Potter dan Batu Bertuah',
                'author' => 'J.K. Rowling',
                'publisher' => 'Gramedia Pustaka Utama',
                'publication_year' => 2000,
                'category' => 'Fantasi',
                'description' => 'Buku pertama dari serial Harry Potter.',
                'total_copies' => 5,
                'available_copies' => 2,
                'language' => 'Indonesian',
                'pages' => 336,
            ],
            
            // Cerita Anak
            [
                'isbn' => '978-602-291-135-1',
                'title' => 'Si Kancil dan Buaya',
                'author' => 'Anonim',
                'publisher' => 'Elex Media Komputindo',
                'publication_year' => 2015,
                'category' => 'Cerita Anak',
                'description' => 'Cerita rakyat klasik Indonesia untuk anak-anak.',
                'total_copies' => 6,
                'available_copies' => 6,
                'language' => 'Indonesian',
                'pages' => 48,
            ],
            
            // Islam
            [
                'isbn' => '978-602-291-136-8',
                'title' => 'Sirah Nabawiyah',
                'author' => 'Syaikh Shafiyyurrahman al-Mubarakfuri',
                'publisher' => 'Pustaka Al-Kautsar',
                'publication_year' => 2010,
                'category' => 'Islam',
                'description' => 'Sejarah kehidupan Nabi Muhammad SAW.',
                'total_copies' => 4,
                'available_copies' => 3,
                'language' => 'Indonesian',
                'pages' => 800,
            ],
        ];

        foreach ($books as $bookData) {
            $categoryName = $bookData['category'];
            unset($bookData['category']);
            
            $category = Category::where('name', $categoryName)->first();
            
            if ($category) {
                $bookData['category_id'] = $category->id;
                Book::create($bookData);
            }
        }
    }
}
