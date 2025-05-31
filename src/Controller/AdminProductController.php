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

#[Route('/admin/products')]
#[IsGranted('ROLE_ADMIN')]
class AdminProductController extends AbstractController
{
    #[Route('/', name: 'admin_products')]
    public function index(SweatshirtRepository $repo): Response
    {
        return $this->render('admin/products/index.html.twig', [
            'sweatshirts' => $repo->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_product_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $sweatshirt = new Sweatshirt();
        $form = $this->createForm(SweatshirtType::class, $sweatshirt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stock = $request->request->all('stock');
            $sweatshirt->setStockXS($stock['XS'] ?? 0);
            $sweatshirt->setStockS($stock['S'] ?? 0);
            $sweatshirt->setStockM($stock['M'] ?? 0);
            $sweatshirt->setStockL($stock['L'] ?? 0);
            $sweatshirt->setStockXL($stock['XL'] ?? 0);

            $em->persist($sweatshirt);
            $em->flush();

            $this->addFlash('success', '✅ Produit créé');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_product_edit')]
    public function edit(Sweatshirt $sweatshirt, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SweatshirtType::class, $sweatshirt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stock = $request->request->all('stock');
            $sweatshirt->setStockXS($stock['XS'] ?? 0);
            $sweatshirt->setStockS($stock['S'] ?? 0);
            $sweatshirt->setStockM($stock['M'] ?? 0);
            $sweatshirt->setStockL($stock['L'] ?? 0);
            $sweatshirt->setStockXL($stock['XL'] ?? 0);

            $em->flush();
            $this->addFlash('success', '✅ Produit mis à jour');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/edit.html.twig', [
            'form' => $form->createView(),
            'sweatshirt' => $sweatshirt,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_product_delete', methods: ['POST'])]
    public function delete(Sweatshirt $sweatshirt, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_' . $sweatshirt->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('admin_products');
        }

        if (!$sweatshirt->getOrderItems()->isEmpty()) {
            $this->addFlash('danger', '❌ Ce produit a déjà été commandé.');
            return $this->redirectToRoute('admin_products');
        }

        $em->remove($sweatshirt);
        $em->flush();

        $this->addFlash('success', '🗑️ Produit supprimé');
        return $this->redirectToRoute('admin_products');
    }
}
