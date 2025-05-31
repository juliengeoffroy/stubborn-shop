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
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin')]
    public function index(SweatshirtRepository $sweatshirtRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'sweatshirts' => $sweatshirtRepository->findAll(),
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_sweatshirt_edit')]
    public function edit(Sweatshirt $sweatshirt, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SweatshirtType::class, $sweatshirt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stockData = $request->request->all('stock');
            $sweatshirt->setStockXS($stockData['XS'] ?? 2);
            $sweatshirt->setStockS($stockData['S'] ?? 2);
            $sweatshirt->setStockM($stockData['M'] ?? 2);
            $sweatshirt->setStockL($stockData['L'] ?? 2);
            $sweatshirt->setStockXL($stockData['XL'] ?? 2);

            $em->flush();
            $this->addFlash('success', 'Sweat-shirt mis à jour avec succès.');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'sweatshirt' => $sweatshirt,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_sweatshirt_delete', methods: ['POST'])]
    public function delete(Sweatshirt $sweatshirt, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_'.$sweatshirt->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('admin');
        }

        if (!$sweatshirt->getOrderItems()->isEmpty()) {
            $this->addFlash('danger', 'Impossible de supprimer : ce produit a déjà été commandé.');
            return $this->redirectToRoute('admin');
        }

        $em->remove($sweatshirt);
        $em->flush();
        $this->addFlash('success', 'Sweatshirt supprimé.');

        return $this->redirectToRoute('admin');
    }

    #[Route('/create', name: 'admin_sweatshirt_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $sweatshirt = new Sweatshirt();
        $form = $this->createForm(SweatshirtType::class, $sweatshirt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stockData = $request->request->all('stock');
            $sweatshirt->setStockXS($stockData['XS'] ?? 2);
            $sweatshirt->setStockS($stockData['S'] ?? 2);
            $sweatshirt->setStockM($stockData['M'] ?? 2);
            $sweatshirt->setStockL($stockData['L'] ?? 2);
            $sweatshirt->setStockXL($stockData['XL'] ?? 2);

            $em->persist($sweatshirt);
            $em->flush();

            $this->addFlash('success', 'Sweatshirt créé avec succès.');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
