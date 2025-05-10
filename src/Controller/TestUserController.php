<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestUserController extends AbstractController
{
    #[Route('/show-users', name: 'show_users')]
    public function index(UserRepository $repo): Response
    {
        $users = $repo->findAll();

        return new Response(
            implode('<br>', array_map(fn($u) => $u->getEmail() . ' - ' . $u->getPassword(), $users))
        );
    }
}
