<?php

namespace App\Controller;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function checkout(EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        // Pour tester sans être connecté
        if (!$user) {
            $user = $em->getRepository(\App\Entity\User::class)->find(1);
        }

        $cart = $em->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart || $cart->getCartItems()->isEmpty()) {
            $this->addFlash('danger', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $lineItems = [];

        foreach ($cart->getCartItems() as $item) {
            $imageUrl = $this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
                . 'images/' . $item->getSweatshirt()->getImage();

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $item->getSweatshirt()->getPrice() * 100,
                    'product_data' => [
                        'name' => $item->getSweatshirt()->getName(),
                        'images' => [$imageUrl], // ✅ Image Stripe
                    ],
                ],
                'quantity' => $item->getQuantity(),
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_cart', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url);
    }

    #[Route('/checkout/success', name: 'checkout_success')]
    public function success(EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            $user = $em->getRepository(\App\Entity\User::class)->find(1);
        }

        if ($user) {
            $cart = $em->getRepository(Cart::class)->findOneBy(['user' => $user]);

            if ($cart) {
                foreach ($cart->getCartItems() as $item) {
                    $cart->removeCartItem($item);
                    $em->remove($item);
                }
                $em->flush();
            }
        }

        $this->addFlash('success', '✅ Paiement réussi, votre commande est validée.');
        return $this->redirectToRoute('home');
    }
}
