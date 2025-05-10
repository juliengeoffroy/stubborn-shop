<?php

namespace App\Controller;

use App\Repository\SweatshirtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')] // 🔐 Sécurité : accès réservé aux admins
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin')]
    public function index(SweatshirtRepository $sweatshirtRepository): Response
    {
        $sweatshirts = $sweatshirtRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'sweatshirts' => $sweatshirts
        ]);
    }
}
