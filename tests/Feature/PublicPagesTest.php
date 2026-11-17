<?php

namespace Tests\Feature;

use App\Models\FoodProduct;
use App\Models\GroceryCategory;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_information_pages_render(): void
    {
        $this->get(route('about'))->assertOk()->assertSee('A marketplace that brings local groceries closer');
        $this->get(route('policy'))->assertOk()->assertSee('Privacy policy');
        $this->get(route('tos'))->assertOk()->assertSee('Terms of service');
    }

    public function test_product_detail_page_renders_product_and_vendor_information(): void
    {
        $vendor = Vendor::create([
            'name' => 'Fresh Test Store',
            'email' => 'fresh-store@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'vendor',
            'address' => 'Port Moresby',
            'description' => 'A test grocery store',
        ]);
        $category = GroceryCategory::create(['name' => 'Test Produce']);
        $product = FoodProduct::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Test Bananas',
            'description' => 'Fresh test bananas',
            'price' => 5.50,
            'image' => 'https://images.unsplash.com/test-image',
            'stock' => 12,
        ]);

        $this->get(route('product.show', ['id' => $product->id]))
            ->assertOk()
            ->assertSee('Test Bananas')
            ->assertSee('Fresh Test Store')
            ->assertSee('Fresh test bananas');
    }
    public function test_category_search_returns_html_results_and_live_search_json(): void
    {
        $vendor = Vendor::create([
            'name' => 'Search Test Store',
            'email' => 'search-store@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'vendor',
            'address' => 'Port Moresby',
            'description' => 'Search fixture',
        ]);
        $category = GroceryCategory::create(['name' => 'Search Vegetables']);
        FoodProduct::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Search Fresh Cabbage',
            'description' => 'Fresh cabbage for testing category search',
            'price' => 4.50,
            'image' => 'https://images.unsplash.com/test-image',
            'stock' => 8,
        ]);

        $this->get(route('search', ['q' => 'Search Vegetables']))
            ->assertOk()
            ->assertSee('Search Fresh Cabbage');

        $this->getJson(route('search', ['q' => 'Search Vegetables']))
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Search Fresh Cabbage');
    }
}
