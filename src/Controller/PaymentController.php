<?php

namespace App\Controller;

use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PaymentController extends AbstractController
{
    #[Route('/success', name: 'payment_success')]
    public function success(
        Security $security,
        CartRepository $cartRepository,
        EntityManagerInterface $em
    ): Response {
        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // On récupère le panier de l'utilisateur
        $cart = $cartRepository->findOneBy(['user' => $user]);

        if ($cart) {
            // On supprime les items du panier
            foreach ($cart->getCartItems() as $item) {
                $em->remove($item);
            }

            $em->flush();
        }

        $this->addFlash('success', 'Paiement réussi ! Votre panier a été vidé.');

        return $this->render('payment/success.html.twig');
    }

    #[Route('/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('warning', 'Le paiement a été annulé.');

        return $this->redirectToRoute('cart');
    }
}
