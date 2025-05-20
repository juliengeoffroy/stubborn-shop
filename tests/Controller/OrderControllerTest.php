<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
    public function testAccessCheckout()
    {
        $client = static::createClient();

        // Récupération du container et du repository
        $userRepository = static::getContainer()->get(\App\Repository\UserRepository::class);

        // 🔍 Astuce : tu peux créer un utilisateur test directement ici
        $testUser = $userRepository->findOneBy(['email' => 'test@example.com']);

        // ✅ Vérifie que le user existe bien
        $this->assertNotNull($testUser, 'L\'utilisateur test@example.com est introuvable. Crée-le via les fixtures.');

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/checkout');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Paiement'); // adapte si ton H1 dit autre chose
    }
}
