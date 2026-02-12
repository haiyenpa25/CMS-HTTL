<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Assets\Models\AssetCategory;

class AssetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = AssetCategory::getDefaultCategories();

        foreach ($categories as $category) {
            AssetCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
