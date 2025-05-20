<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Sweatshirt;
use App\Entity\User;

class CartTest extends KernelTestCase
{
    public function testTotalPanier()
    {
        $cart = new Cart();
        $user = new User();
        $cart->setUser($user);

        $sweat = new Sweatshirt();
        $sweat->setName("Blackbelt");
        $sweat->setPrice(2990); // 29,90€

        $item = new CartItem();
        $item->setSweatshirt($sweat);
        $item->setQuantity(2);
        $item->setCart($cart);

        $cart->addCartItem($item);

        $total = 0;
        foreach ($cart->getCartItems() as $i) {
            $total += $i->getSweatshirt()->getPrice() * $i->getQuantity();
        }

        $this->assertEquals(5980, $total); // 29.90 x 2 = 59.80 => 5980 en centimes
    }
}
