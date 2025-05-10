<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestPasswordController extends AbstractController
{
    #[Route('/test-password', name: 'test_password')]
    public function index(EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'admin@admin.com']);

        if (!$user) {
            return new Response("❌ Utilisateur introuvable");
        }

        $isValid = $hasher->isPasswordValid($user, 'Geoffroy01');

        if ($isValid) {
            return new Response("✅ Mot de passe VALIDE");
        } else {
            return new Response("❌ Mot de passe INVALIDE");
        }
    }
}
