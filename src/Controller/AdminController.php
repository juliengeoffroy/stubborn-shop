<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use App\Form\SweatshirtType;
use App\Repository\SweatshirtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')] // 🔐 Accès admin uniquement
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/', name: 'admin')]
    public function index(SweatshirtRepository $sweatshirtRepository): Response
    {
        $sweatshirts = $sweatshirtRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'sweatshirts' => $sweatshirts
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_sweatshirt_edit')]
    public function edit(Sweatshirt $sweatshirt, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SweatshirtType::class, $sweatshirt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 💡 Récupérer les stocks manuellement
            $stockData = $request->request->all('stock'); // ex : ['XS' => 2, 'S' => 3, ...]
            $sweatshirt->setStock($stockData);

            $em->flush();

            $this->addFlash('success', 'Sweatshirt mis à jour avec succès.');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'sweatshirt' => $sweatshirt,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_sweatshirt_delete', methods: ['POST'])]
    public function delete(
        Sweatshirt $sweatshirt,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete_' . $sweatshirt->getId(), $request->request->get('_token'))) {
            $em->remove($sweatshirt);
            $em->flush();
            $this->addFlash('success', 'Sweatshirt supprimé');
        }

        return $this->redirectToRoute('admin');
    }
}
