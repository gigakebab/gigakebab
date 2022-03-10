<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\ProductLine;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order', name: 'order_')]
class OrderController extends AbstractController
{
    #[Route('/create', name: 'app_create')]
    public function create(SessionInterface $session, ProductRepository $repo, UserRepository $userRepo, EntityManagerInterface $manager): Response
    {
        $cart = $session->get("cart");

        $order = new Order();

        $user = $userRepo->find($this->getUser());

        $totalPrice = 0;

        $order->setUser($user)
              ->setAddress($user->getAddresses()[0])
              ->setStatus(false)
              ->setDeliveryAt(new DateTime())
              ->setOderAt(new DateTime());

        foreach ($cart as $productId => $quantity){
            $productLine = new ProductLine();
            $product = $repo->find($productId);
            $productLine->setProduct($product)
                        ->setPrice($product->getPrice() * $quantity)
                        ->setQuantity($quantity)
                        ->setOrderProduct($order);
            $totalPrice += $productLine->getPrice();
            $manager->persist($productLine);
        }

        $order->setPrice($totalPrice);
        $manager->persist($order);

        $manager->flush();

        return $this->redirectToRoute('order_validate', [
            "order" => $order->getId()
        ]);
    }


    #[Route('/validate/{order}', name: 'validate')]
    public function validate(Order $order): Response
    {
        return $this->render('order/index.html.twig', [
            'order' => $order
        ]);
    }
}
