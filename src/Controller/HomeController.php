<?php

namespace App\Controller;

use App\Repository\SweatshirtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(SweatshirtRepository $sweatshirtRepository): Response
    {
        // On récupère les sweats mis en avant (champ isFeatured = true)
        $featuredSweats = $sweatshirtRepository->findBy(['isFeatured' => true]);

        return $this->render('home/index.html.twig', [
            'featuredSweats' => $featuredSweats,
        ]);
    }
}
