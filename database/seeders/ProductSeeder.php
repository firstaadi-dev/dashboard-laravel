<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure categories exist first
        if (Category::count() === 0) {
            Category::factory()->count(5)->create();
        }

        // Get all categories
        $categories = Category::all();

        // Create 100,000 products in chunks of 1,000 to avoid memory exhaustion
        $total = 100000;
        $chunkSize = 1000;
        $chunks = ceil($total / $chunkSize);

        for ($i = 0; $i < $chunks; $i++) {
            Product::factory()
                ->count($chunkSize)
                ->recycle($categories)
                ->create();

            $this->command->info("Created " . (($i + 1) * $chunkSize) . " / $total products");
        }
    }
}
