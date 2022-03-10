<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProductLineController extends AbstractController
{
    #[Route('/productLine', name: 'toto')]
    public function create(SessionInterface $session, ProductRepository $repo, User $user): Response
    {
        $cart = $session->get("cart");

        foreach ($cart as $productId => $quantity){
            $product = $repo->find($productId);
        }

//        $order->setUser($user)
//              ->setPrice()
//              ->setAddress()
        return $this->render('order/index.html.twig', [

        ]);
    }
}
