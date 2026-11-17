<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\FoodProduct;
use App\Models\GroceryCategory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $password = (string) env('GROWGREEN_DEMO_PASSWORD');
        if (strlen($password) < 12) {
            throw new RuntimeException('Set GROWGREEN_DEMO_PASSWORD to a unique password of at least 12 characters before enabling demo seeding.');
        }

        Admin::firstOrCreate(
            ['email' => 'admin.demo@growgreen.test'],
            ['name' => 'Growgreen Demo Admin', 'password' => Hash::make($password), 'role' => 'admin', 'email_verified_at' => now()]
        );

        User::firstOrCreate(
            ['email' => 'customer.demo@growgreen.test'],
            ['name' => 'Demo Customer', 'password' => Hash::make($password), 'role' => 'user', 'city' => 'Port Moresby', 'state' => 'National Capital District']
        );

        $vendor = Vendor::firstOrCreate(
            ['email' => 'vendor.demo@growgreen.test'],
            [
                'name' => 'Fresh Market Demo Store',
                'password' => Hash::make($password),
                'role' => 'vendor',
                'address' => 'Gordons, Port Moresby',
                'description' => 'Demonstration grocery store with locally inspired fresh produce.',
                'status' => 'open',
                'verification' => 'approved',
            ]
        );

        $catalogue = [
            ['Fruits', 'Fresh PNG Bananas', 'Ripe locally sourced bananas, ideal for breakfast and snacks.', 8.50, 'kg', 'photo-1571771894821-ce9b6c11b08e'],
            ['Fruits', 'Sweet Pineapple', 'Juicy sweet pineapple, prepared fresh for your table.', 12.00, 'each', 'photo-1550258987-190a2d41a8ba'],
            ['Fruits', 'Red Apples', 'Crisp red apples packed with natural sweetness.', 15.00, 'kg', 'photo-1560806887-1e4cd0b6cbd6'],
            ['Vegetables', 'Fresh Tomatoes', 'Firm garden tomatoes for salads, sauces and cooking.', 10.00, 'kg', 'photo-1546094096-0df4bcaaa337'],
            ['Vegetables', 'Green Broccoli', 'Fresh green broccoli florets, rich in everyday goodness.', 14.00, 'kg', 'photo-1459411621453-7b03977f4bfc'],
            ['Vegetables', 'Carrots', 'Crunchy fresh carrots, great for soups and side dishes.', 9.00, 'kg', 'photo-1445282768818-728615cc910a'],
            ['Dairy & Eggs', 'Farm Fresh Eggs', 'Carefully packed eggs for everyday meals.', 18.00, 'dozen', 'photo-1506976785307-8732e854ad03'],
            ['Bakery', 'Wholemeal Bread', 'Soft wholemeal loaf baked for daily enjoyment.', 9.50, 'loaf', 'photo-1509440159596-0249088772ff'],
            ['Beverages', 'Orange Juice', 'Refreshing orange juice for the whole family.', 11.00, 'bottle', 'photo-1600271886742-f049cd451bba'],
            ['Pantry', 'White Rice', 'Quality everyday rice for family meals.', 16.00, 'kg', 'photo-1586201375761-83865001e31c'],
        ];

        foreach ($catalogue as [$categoryName, $name, $description, $price, $unit, $photo]) {
            $category = GroceryCategory::firstOrCreate(['name' => $categoryName]);
            FoodProduct::firstOrCreate(
                ['vendor_id' => $vendor->id, 'name' => $name],
                [
                    'category_id' => $category->id,
                    'description' => $description,
                    'price' => $price,
                    'image' => 'https://images.unsplash.com/'.$photo.'?auto=format&fit=crop&w=900&q=80',
                    'stock' => 40,
                    'unit' => $unit,
                ]
            );
        }
    }
}
