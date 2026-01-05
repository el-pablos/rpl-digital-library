<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@perpustakaan.test',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Jl. Perpustakaan No. 1, Jakarta',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create Librarian users
        $librarians = [
            [
                'name' => 'Rina Pustakawan',
                'email' => 'rina@perpustakaan.test',
                'phone' => '081234567891',
                'address' => 'Jl. Pustaka No. 2, Jakarta',
            ],
            [
                'name' => 'Budi Sirkulasi',
                'email' => 'budi@perpustakaan.test',
                'phone' => '081234567892',
                'address' => 'Jl. Pustaka No. 3, Jakarta',
            ],
        ];

        foreach ($librarians as $librarianData) {
            $librarian = User::create([
                'name' => $librarianData['name'],
                'email' => $librarianData['email'],
                'password' => Hash::make('password'),
                'phone' => $librarianData['phone'],
                'address' => $librarianData['address'],
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $librarian->assignRole('librarian');
        }

        // Create Member users (sample members)
        $members = [
            [
                'name' => 'Sarah Mahasiswa',
                'email' => 'sarah@student.test',
                'phone' => '082345678901',
                'address' => 'Jl. Mahasiswa No. 1, Bandung',
            ],
            [
                'name' => 'Dr. Ahmad Dosen',
                'email' => 'ahmad@dosen.test',
                'phone' => '082345678902',
                'address' => 'Jl. Akademik No. 2, Bandung',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@student.test',
                'phone' => '082345678903',
                'address' => 'Jl. Pelajar No. 3, Surabaya',
            ],
            [
                'name' => 'Rudi Hartono',
                'email' => 'rudi@student.test',
                'phone' => '082345678904',
                'address' => 'Jl. Kampus No. 4, Yogyakarta',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@student.test',
                'phone' => '082345678905',
                'address' => 'Jl. Universitas No. 5, Semarang',
            ],
            [
                'name' => 'Andi Prasetyo',
                'email' => 'andi@student.test',
                'phone' => '082345678906',
                'address' => 'Jl. Ilmu No. 6, Medan',
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya@student.test',
                'phone' => '082345678907',
                'address' => 'Jl. Pendidikan No. 7, Makassar',
            ],
            [
                'name' => 'Fajar Ramadhan',
                'email' => 'fajar@student.test',
                'phone' => '082345678908',
                'address' => 'Jl. Belajar No. 8, Palembang',
            ],
            [
                'name' => 'Linda Wati',
                'email' => 'linda@student.test',
                'phone' => '082345678909',
                'address' => 'Jl. Sekolah No. 9, Denpasar',
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'hendra@student.test',
                'phone' => '082345678910',
                'address' => 'Jl. Kuliah No. 10, Malang',
            ],
        ];

        foreach ($members as $memberData) {
            $member = User::create([
                'name' => $memberData['name'],
                'email' => $memberData['email'],
                'password' => Hash::make('password'),
                'phone' => $memberData['phone'],
                'address' => $memberData['address'],
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $member->assignRole('member');
        }

        // Create one suspended member for testing
        $suspendedMember = User::create([
            'name' => 'User Suspended',
            'email' => 'suspended@student.test',
            'password' => Hash::make('password'),
            'phone' => '082345678999',
            'address' => 'Jl. Suspend No. 99',
            'status' => 'suspended',
            'email_verified_at' => now(),
        ]);
        $suspendedMember->assignRole('member');
    }
}
