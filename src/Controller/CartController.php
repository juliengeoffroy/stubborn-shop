<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Sweatshirt;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser() ?: $em->getRepository(User::class)->find(1);

        $cart = $em->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart || $cart->getCartItems()->isEmpty()) {
            return $this->render('cart/index.html.twig', [
                'cartItems' => [],
                'total' => 0,
            ]);
        }

        $total = 0;
        foreach ($cart->getCartItems() as $item) {
            $total += $item->getQuantity() * $item->getSweatshirt()->getPrice();
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cart->getCartItems(),
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add(Sweatshirt $sweatshirt, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser() ?: $em->getRepository(User::class)->find(1);

        $cart = $em->getRepository(Cart::class)->findOneBy(['user' => $user]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $em->persist($cart);
        }

        $existingItem = null;
        foreach ($cart->getCartItems() as $item) {
            if ($item->getSweatshirt()->getId() === $sweatshirt->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setSweatshirt($sweatshirt);
            $cartItem->setQuantity(1);
            $em->persist($cartItem);
        }

        $em->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeItem(CartItem $item, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser() ?: $em->getRepository(User::class)->find(1);

        if ($item->getCart()->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($item);
        $em->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser() ?: $em->getRepository(User::class)->find(1);

        $cart = $em->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if ($cart) {
            foreach ($cart->getCartItems() as $item) {
                $em->remove($item);
            }
            $em->flush();
        }

        return $this->redirectToRoute('app_cart');
    }
}

