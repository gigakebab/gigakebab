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
    public function index(SessionInterface $session, ProductRepository $repo): Response
    {
        $totalPrice = 0;
        $products = [];

        if ($session->get('cart')){
            foreach ($session->get('cart') as $productId){
                $product = $repo->find($productId);
                $products[] = $product;

                $totalPrice += $product->getPrice();
            }
        }

        return $this->render('cart/index.html.twig', [
            'products' => $products,
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/buy/{id}', name: 'app_buy')]
    public function buy(Product $product, SessionInterface $session, ProductRepository $repo): Response
    {
        $cart = $session->get("cart");
//        dd($cart);
//        $cart[$product->getId()] ? $cart[$product->getId()] += 1 : $cart[$product->getId()] = 1;
        //$cart[$product->getId()] += 1;

        foreach ($cart as $id => $quantity){
            $result = $repo->find($id);
            if(!$result){

            }
        }

        $session->set("cart", $cart);

        return $this->redirectToRoute('product_show', []);
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
