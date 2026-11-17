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

        $stores = [
            [
                'email' => 'vendor.demo@growgreen.test',
                'name' => 'Fresh Market Demo Store',
                'address' => 'Gordons, Port Moresby',
                'description' => 'Locally inspired fresh fruit, vegetables and everyday groceries.',
                'avatar' => 'photo-1534528741775-53994a69daeb',
                'products' => [
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
                ],
            ],
            [
                'email' => 'vendor.fresh@growgreen.test',
                'name' => 'Garden Basket Produce',
                'address' => 'Boroko, Port Moresby',
                'description' => 'A neighbourhood produce stall with fresh vegetables and seasonal fruit.',
                'avatar' => 'photo-1500648767791-00dcc994a43e',
                'products' => [
                    ['Vegetables', 'Sweet Corn', 'Fresh sweet corn for steaming, grilling and family meals.', 6.00, 'each', 'photo-1551754655-cd27e38d2076'],
                    ['Vegetables', 'Green Capsicum', 'Crisp green capsicum for stir fries and salads.', 12.00, 'kg', 'photo-1563565375-f3fdfdbefa83'],
                    ['Fruits', 'Ripe Mangoes', 'Sweet seasonal mangoes selected for ripeness.', 7.50, 'each', 'photo-1553279768-865429fa0078'],
                ],
            ],
            [
                'email' => 'vendor.harvest@growgreen.test',
                'name' => 'Highlands Harvest Store',
                'address' => 'Waigani, Port Moresby',
                'description' => 'Pantry staples and fresh market picks for everyday cooking.',
                'avatar' => 'photo-1531123897727-8f129e1688ce',
                'products' => [
                    ['Pantry', 'Brown Rice', 'Nutty wholegrain rice for wholesome meals.', 18.00, 'kg', 'photo-1516684732162-798a0062be99'],
                    ['Bakery', 'Fresh Dinner Rolls', 'Soft rolls suitable for lunch and dinner.', 8.00, 'pack', 'photo-1608198093002-ad4e005484df'],
                    ['Beverages', 'Fresh Lemons', 'Bright citrus lemons for drinks and cooking.', 10.00, 'kg', 'photo-1518977676601-b53f82aba655'],
                ],
            ],
            [
                'email' => 'vendor.coastal@growgreen.test',
                'name' => 'Coastal Family Grocer',
                'address' => 'Boroko, Port Moresby',
                'description' => 'A family-run demo store with pantry favourites and daily essentials.',
                'avatar' => 'photo-1544005313-94ddf0286df2',
                'products' => [
                    ['Dairy & Eggs', 'Free Range Eggs', 'Fresh eggs packed for daily family use.', 20.00, 'dozen', 'photo-1518569656558-1f25e69d93d7'],
                    ['Pantry', 'Rolled Oats', 'Versatile oats for breakfast and baking.', 14.00, 'pack', 'photo-1517673132405-a56a62b18caf'],
                    ['Fruits', 'Green Grapes', 'Sweet crisp grapes, washed and ready to enjoy.', 17.00, 'kg', 'photo-1537640538966-79f369143f8f'],
                ],
            ],
        ];

        foreach ($stores as $store) {
            $vendor = Vendor::firstOrCreate(
                ['email' => $store['email']],
                [
                    'name' => $store['name'],
                    'password' => Hash::make($password),
                    'role' => 'vendor',
                    'address' => $store['address'],
                    'description' => $store['description'],
                    'status' => 'open',
                    'verification' => 'approved',
                    'profile_picture' => 'https://images.unsplash.com/'.$store['avatar'].'?auto=format&fit=crop&w=320&h=320&q=80',
                ]
            );

            if (! $vendor->profile_picture) {
                $vendor->update(['profile_picture' => 'https://images.unsplash.com/'.$store['avatar'].'?auto=format&fit=crop&w=320&h=320&q=80']);
            }

            foreach ($store['products'] as [$categoryName, $name, $description, $price, $unit, $photo]) {
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
}
