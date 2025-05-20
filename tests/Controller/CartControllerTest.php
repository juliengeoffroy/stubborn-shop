<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    public function testAddToCart(): void
    {
        $client = static::createClient();
        $client->followRedirects(); // pour suivre la redirection après ajout

        // ⚠️ Sweatshirt avec ID 1 doit exister dans ta BDD de test
        $client->request('GET', '/cart/add/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.cart-item'); // adapte ce sélecteur à ton HTML
    }

    public function testClearCart(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/cart/clear');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('.cart-item'); // panier vide
    }
}
