<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Sweatshirt;
use App\Repository\SweatshirtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'products')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $min = $request->query->get('min');
        $max = $request->query->get('max');

        $repo = $em->getRepository(Sweatshirt::class);
        $queryBuilder = $repo->createQueryBuilder('s');

        if ($min && $max) {
            $queryBuilder->where('s.price >= :min')
                         ->andWhere('s.price <= :max')
                         ->setParameter('min', $min)
                         ->setParameter('max', $max);
        }

        $sweats = $queryBuilder->getQuery()->getResult();

        return $this->render('product/index.html.twig', [
            'sweats' => $sweats
        ]);
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(Sweatshirt $sweatshirt, Request $request): Response
    {
        $referer = $request->headers->get('referer');

        return $this->render('product/show.html.twig', [
            'sweatshirt' => $sweatshirt,
            'backUrl' => $referer ?: $this->generateUrl('products'), // redirige vers boutique si pas de referer
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(
        Sweatshirt $sweatshirt,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        // 💬 Commenter ce bloc pour désactiver temporairement la vérif connexion
        /*
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        */

        // Utilisateur "factice" temporaire pour dev (à commenter ou supprimer en prod)
        if (!$user) {
            $user = $em->getRepository(\App\Entity\User::class)->find(1); // 🔁 Mets ici un ID valide
        }

        $size = $request->request->get('size');
        $quantity = (int) $request->request->get('quantity', 1);

        $cart = $em->getRepository(Cart::class)->findOneBy(['user' => $user]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setCreatedAt(new \DateTimeImmutable());
            $em->persist($cart);
        }

        $item = new CartItem();
        $item->setCart($cart);
        $item->setSweatshirt($sweatshirt);
        $item->setSize($size);
        $item->setQuantity($quantity);
        $em->persist($item);

        $em->flush();

        return $this->redirectToRoute('app_cart');
    }
}
