<?php

namespace Database\Seeders;

use App\Models\GroceryCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Fruits',
            'Vegetables',
            'Meat & Seafood',
            'Dairy & Eggs',
            'Bakery',
            'Pantry',
            'Beverages',
            'Snacks',
        ] as $name) {
            GroceryCategory::firstOrCreate(['name' => $name]);
        }
    }
}
