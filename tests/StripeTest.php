<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeTest extends TestCase
{
    public function testStripeSessionCreation()
    {
        Stripe::setApiKey('sk_test_51REvjX2acZ3TexD7R4QM5uIhcV0hk5oow6eXg79cOhwnZn0AksPgDru4GCy5OWtgiz5ReSJHLpKYnz3kLU3Enmm700m784706i'); // mets ici ta vraie clé test

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => 1000, // 10€
                    'product_data' => [
                        'name' => 'Test Sweatshirt',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost/success',
            'cancel_url' => 'http://localhost/cancel',
        ]);

        $this->assertNotEmpty($session->id);
        $this->assertStringContainsString('cs_test_', $session->id);
    }
}
