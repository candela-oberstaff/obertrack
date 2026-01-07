<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Superadmin creation
        $admin = User::firstOrCreate(
            ['email' => 'candela@oberstaff.com'],
            [
                'name' => 'Candela Superadmin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        // Use raw SQL to avoid PostgreSQL boolean mismatch
        \Illuminate\Support\Facades\DB::statement('UPDATE users SET is_superadmin = true WHERE id = ?', [$admin->id]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
