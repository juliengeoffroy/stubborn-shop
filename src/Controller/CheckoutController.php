<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\SecurityBundle\Security;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $cart = $em->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart || $cart->getCartItems()->isEmpty()) {
            $this->addFlash('danger', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $lineItems = [];

        foreach ($cart->getCartItems() as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $item->getSweatshirt()->getPrice() * 100,
                    'product_data' => [
                        'name' => $item->getSweatshirt()->getName(),
                        'images' => [
                            $this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL) .
                            'images/' . $item->getSweatshirt()->getImage()
                        ],
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
    public function success(EntityManagerInterface $em, Security $security, MailerInterface $mailer): Response
    {
        $user = $security->getUser();
        $cart = $em->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart || $cart->getCartItems()->isEmpty()) {
            return $this->redirectToRoute('home');
        }

        $order = new Order();
        $order->setUser($user);
        $order->setCreatedAt(new \DateTime());
        $order->setPaymentId(uniqid('pay_'));
        $order->setStatus('payé');

        $total = 0;

        foreach ($cart->getCartItems() as $item) {
            $sweatshirt = $item->getSweatshirt();
            $qty = $item->getQuantity();
            $size = $item->getSize();

            // Décrémenter le stock
            switch ($size) {
                case 'XS': $sweatshirt->setStockXS($sweatshirt->getStockXS() - $qty); break;
                case 'S':  $sweatshirt->setStockS($sweatshirt->getStockS() - $qty); break;
                case 'M':  $sweatshirt->setStockM($sweatshirt->getStockM() - $qty); break;
                case 'L':  $sweatshirt->setStockL($sweatshirt->getStockL() - $qty); break;
                case 'XL': $sweatshirt->setStockXL($sweatshirt->getStockXL() - $qty); break;
            }

            $orderItem = new OrderItem();
            $orderItem->setSweatshirt($sweatshirt);
            $orderItem->setQuantity($qty);
            $orderItem->setPrice($sweatshirt->getPrice());
            $orderItem->setOrder($order);

            $em->persist($orderItem);
            $order->addOrderItem($orderItem);

            $total += $sweatshirt->getPrice() * $qty;

            $em->remove($item);
        }

        $order->setTotal($total);
        $em->persist($order);

        // Supprimer le panier pour éviter la duplication
        $em->remove($cart);

        $em->flush();

        // Email de confirmation
        if ($user->getEmail()) {
            $email = (new Email())
                ->from('contact@stubborn-shop.fr')
                ->to($user->getEmail())
                ->subject('Confirmation de commande')
                ->html("
                    <h2>Merci pour votre commande</h2>
                    <p>Total payé : <strong>{$total}€</strong></p>
                    <p>Votre commande est bien enregistrée et en cours de traitement.</p>
                ");
            $mailer->send($email);
        }
        $this->addFlash('success', '✅ Paiement validé. Merci pour votre commande !');
        return $this->redirectToRoute('checkout_confirmation', ['id' => $order->getId()]);
    }
    #[Route('/checkout/confirmation/{id}', name: 'checkout_confirmation')]
    public function confirmation(Order $order): Response
    {
        return $this->render('checkout/confirmation.html.twig', [
            'order' => $order
        ]);
    }

}
