<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\FoodProduct;
use App\Models\GroceryCategory;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_management_pages_are_protected_and_render(): void
    {
        foreach (['admin.products.index', 'admin.orders.index', 'admin.settings', 'admin.support'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }

        $admin = Admin::create([
            'name' => 'Management Admin',
            'email' => 'management-admin@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'admin');
        $this->get(route('admin.products.index'))->assertOk()->assertSee('Products');
        $this->get(route('admin.orders.index'))->assertOk()->assertSee('Orders');
        $this->get(route('admin.settings'))->assertOk()->assertSee('Marketplace settings');
        $this->get(route('admin.support'))->assertOk()->assertSee('Admin support');
    }

    public function test_admin_can_update_product_stock_and_vendor_approval(): void
    {
        $admin = Admin::create([
            'name' => 'Management Admin',
            'email' => 'management-admin@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'admin',
        ]);
        $vendor = Vendor::create([
            'name' => 'Management Store',
            'email' => 'management-store@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'vendor',
            'address' => 'Port Moresby',
            'description' => 'Test store',
        ]);
        $category = GroceryCategory::create(['name' => 'Test Produce']);
        $product = FoodProduct::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Test Apples',
            'description' => 'Test product',
            'price' => 10,
            'image' => 'https://images.unsplash.com/test-image',
            'stock' => 5,
        ]);

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.products.update', $product), ['price' => 12.50, 'stock' => 30])
            ->assertRedirect();

        $this->assertDatabaseHas('food_products', ['id' => $product->id, 'price' => 12.50, 'stock' => 30]);

        $this->patch(route('admin.vendors.update', $vendor), ['status' => 'open', 'verification' => 'approved'])
            ->assertRedirect();

        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'status' => 'open', 'verification' => 'approved']);
    }

    public function test_admin_can_advance_order_through_allowed_status_transition(): void
    {
        $admin = Admin::create([
            'name' => 'Order Admin',
            'email' => 'order-admin@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'admin',
        ]);
        $vendor = Vendor::create([
            'name' => 'Order Store',
            'email' => 'order-store@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'vendor',
            'address' => 'Port Moresby',
            'description' => 'Test store',
        ]);
        $user = User::create([
            'name' => 'Order Customer',
            'email' => 'order-customer@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'user',
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'product_name' => 'Test Rice',
            'quantity' => 2,
            'price' => 24,
            'status' => 'paid',
        ]);

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.orders.status', $order), ['status' => 'processing'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'processing']);
    }
}
