<?php

namespace App\Controller;

use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security,
        CartRepository $cartRepository
    ): Response {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $cartRepository->findOneBy(['user' => $user]);

        if (!$cart || count($cart->getCartItems()) === 0) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $baseUrl = $request->getSchemeAndHttpHost();

        $lineItems = [];

        foreach ($cart->getCartItems() as $item) {
            $sweatshirt = $item->getSweatshirt();

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $sweatshirt->getPrice() * 100,
                    'product_data' => [
                        'name' => $sweatshirt->getName(),
                        'images' => [$baseUrl . '/images/' . $sweatshirt->getImage()],
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

        return $this->redirect($session->url, 303);
    }

    #[Route('/checkout/success', name: 'checkout_success')]
    public function success(
        EntityManagerInterface $entityManager,
        Security $security,
        CartRepository $cartRepository
    ): Response {
        $user = $security->getUser();
        $cart = $cartRepository->findOneBy(['user' => $user]);

        if ($cart) {
            foreach ($cart->getCartItems() as $item) {
                $entityManager->remove($item);
            }
            $entityManager->flush();
        }

        $this->addFlash('success', '✅ Paiement validé, votre panier est vidé.');
        return $this->redirectToRoute('home');
    }
}