<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Book permissions
            'book.view',
            'book.create',
            'book.edit',
            'book.delete',
            
            // Category permissions
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
            
            // Loan permissions
            'loan.request',
            'loan.view-own',
            'loan.view-all',
            'loan.approve',
            'loan.reject',
            'loan.pickup',
            'loan.return',
            'loan.renew',
            
            // Review permissions
            'review.create',
            'review.edit-own',
            'review.delete-own',
            'review.moderate',
            'review.vote',
            
            // Fine permissions
            'fine.view-own',
            'fine.view-all',
            'fine.pay',
            'fine.waive',
            
            // User management permissions
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            
            // Report permissions
            'report.view',
            
            // Notification permissions
            'notification.view-own',
            
            // Dashboard permissions
            'dashboard.admin',
            'dashboard.librarian',
            'dashboard.member',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin - full access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Librarian - manage loans, view books, manage fines
        $librarianRole = Role::create(['name' => 'librarian']);
        $librarianRole->givePermissionTo([
            'book.view',
            'book.create',
            'book.edit',
            'category.view',
            'category.create',
            'category.edit',
            'loan.view-all',
            'loan.approve',
            'loan.reject',
            'loan.pickup',
            'loan.return',
            'review.moderate',
            'fine.view-all',
            'fine.pay',
            'fine.waive',
            'user.view',
            'dashboard.librarian',
        ]);

        // Member - basic access
        $memberRole = Role::create(['name' => 'member']);
        $memberRole->givePermissionTo([
            'book.view',
            'category.view',
            'loan.request',
            'loan.view-own',
            'loan.renew',
            'review.create',
            'review.edit-own',
            'review.delete-own',
            'review.vote',
            'fine.view-own',
            'notification.view-own',
            'dashboard.member',
        ]);
    }
}
