<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Seeds must be run in order due to dependencies:
     * 1. RoleAndPermissionSeeder - Creates roles and permissions (no dependencies)
     * 2. CategorySeeder - Creates book categories (no dependencies)
     * 3. UserSeeder - Creates users and assigns roles (depends on roles)
     * 4. BookSeeder - Creates books (depends on categories)
     * 5. LoanSeeder - Creates loans (depends on users, books)
     * 6. ReviewSeeder - Creates reviews and votes (depends on users, books)
     * 7. FineSeeder - Creates fines (depends on loans)
     * 8. NotificationSeeder - Creates notifications (depends on users)
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            BookSeeder::class,
            LoanSeeder::class,
            ReviewSeeder::class,
            FineSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
