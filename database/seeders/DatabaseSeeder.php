<?php

namespace Database\Seeders;

use App\Modules\Identity\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        require_once __DIR__ . '/MasterDataSeeder.php';
        require_once __DIR__ . '/StandardTestDataSeeder.php';
        require_once __DIR__ . '/UserAccountSeeder.php';

        $this->call([
            // 1. Master Data (Cấu hình hệ thống)
            MasterDataSeeder::class,

            // 2. User Accounts (Roles, Permissions, Users) - Must run before Test Data if TestData relies on users (it doesn't, but good practice)
            UserAccountSeeder::class,

            // 3. Test Data (Dữ liệu mẫu 50 người)
            StandardTestDataSeeder::class,
        ]);

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
