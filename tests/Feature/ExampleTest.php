<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_guest_can_open_the_login_page(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_guest_is_redirected_to_login_before_opening_the_cart(): void
    {
        $this->get('/user/cart')->assertRedirect('/login');
    }

    public function test_guest_cannot_start_paypal_checkout(): void
    {
        $this->post('/user/paypal/payment')->assertRedirect('/login');
    }

    public function test_password_reset_form_is_available(): void
    {
        $this->get('/reset-password/test-token?email=buyer@example.com')->assertOk();
    }

    public function test_product_search_requires_a_query(): void
    {
        $this->getJson('/search')->assertUnprocessable();
    }
}
