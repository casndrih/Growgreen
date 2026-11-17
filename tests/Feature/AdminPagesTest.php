<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_customer_and_vendor_pages_require_admin_authentication(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
        $this->get(route('admin.vendors.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_view_customer_and_vendor_directories(): void
    {
        $admin = Admin::create([
            'name' => 'Test Administrator',
            'email' => 'admin@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'user',
        ]);

        Vendor::create([
            'name' => 'Test Grocery Store',
            'email' => 'vendor@example.test',
            'password' => Hash::make('a-long-test-password'),
            'role' => 'vendor',
            'address' => 'Port Moresby',
            'description' => 'Test store',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Test Customer');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.vendors.index'))
            ->assertOk()
            ->assertSee('Test Grocery Store');
    }
}
