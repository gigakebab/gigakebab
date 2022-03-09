<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session): Response
    {
        $totalPrice = 0;

        if ($session->get('cart')){
            foreach ($session->get('cart') as $product){
                $totalPrice += $product->getPrice();
            }
        }

        return $this->render('cart/index.html.twig', [
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/buy/{id}', name: 'app_buy')]
    public function buy(Product $product, SessionInterface $session): Response
    {
        $cart = $session->get("cart");

        $cart[] = $product;

        $session->set("cart", $cart);

        return $this->redirectToRoute('product_show', [

        ]);
    }

    #[Route('/erase/{id}', name: 'app_erase')]
    public function erase(ProductRepository $repo, Product $product, SessionInterface $session): Response
    {
        $sessionCart = $session->get("cart");

        $cart = array_filter($sessionCart, function ($item) use ($product) {
            return $item->getId() !== $product->getId();
        });

        $session->set("cart", $cart);

        return $this->redirectToRoute('app_cart', [
            'products' => $repo->findAll()
        ]);
    }
}
