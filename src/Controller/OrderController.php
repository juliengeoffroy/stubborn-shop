<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/orders')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'order_history')]
    #[IsGranted('ROLE_USER')]
    public function history(Security $security, EntityManagerInterface $em): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $orders = $em->getRepository(Order::class)->findBy(['user' => $user], ['createdAt' => 'DESC']);

        return $this->render('order/history.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/{id}', name: 'order_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/show.html.twig', [
            'order' => $order
        ]);
    }
}
