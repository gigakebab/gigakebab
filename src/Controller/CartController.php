<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session, ProductRepository $repo): Response
    {
        $totalPrice = 0;
        $products = [];

        if ($session->get('cart')){
            foreach ($session->get('cart') as $productId => $quantity){
                $product = ["product" => $repo->find($productId), 'quantity' => $quantity];
                $products[] = $product;
                $totalPrice += $product['product']->getPrice() * $quantity;
            }
        }

        return $this->render('cart/index.html.twig', [
            'products' => $products,
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/buy/{id}', name: 'app_buy')]
    public function buy(Product $product, SessionInterface $session, Request $request): Response
    {
        $cart = $session->get("cart");

        !$cart ||!array_key_exists($product->getId(), $cart) ? $cart[$product->getId()] = 1 : $cart[$product->getId()] += 1 ;


        $session->set("cart", $cart);
        return $this->json($cart, 200);
    }

    #[Route('/substract/{id}', name: 'app_substract')]
    public function substract(Product $product, SessionInterface $session, Request $request): Response
    {
        $cart = $session->get("cart");
        array_key_exists($product->getId(), $cart) && $cart[$product->getId()] -= 1 ;

        if($cart[$product->getId()] === 0) unset($cart[$product->getId()]);

        $session->set("cart", $cart);
        return $this->json($cart, 200);
    }

    #[Route('/erase/{id}', name: 'app_erase')]
    public function erase(Product $product, SessionInterface $session): Response
    {
        $sessionCart = $session->get("cart");

        $cart = array_filter($sessionCart, function ($item) use ($product) {
            return $item->getId() !== $product->getId();
        });

        $session->set("cart", $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/deleteAll', name: 'deleteAll')]
    public function deleteAll(SessionInterface $session):Response {
        $session->remove('cart');

        return $this->redirectToRoute('app_cart');
    }
  }
