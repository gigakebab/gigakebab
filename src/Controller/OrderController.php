<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\ProductLine;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route('/order', name: 'order_')]
class OrderController extends AbstractController
{
    #[Route('/create', name: 'app_create')]
    public function create(SessionInterface $session, ProductRepository $repo, UserRepository $userRepo, EntityManagerInterface $manager, OrderRepository $orderRepo): Response
    {
        if(!empty($session->get('order')) && $orderRepo->findOneBy(['id' => $session->get('order')])) {
            $order = $orderRepo->find($session->get('order'));

            $manager->remove($order);
            $manager->flush();
            $session->remove('order');
        }

        $cart = $session->get("cart");

        $order = new Order();

        $user = $userRepo->find($this->getUser());

        if(!$user->getAddresses()[0]) {
            return $this->redirectToRoute('app_cart');
        }

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
        $session->set('order', $order->getId());

        return $this->render('order/index.html.twig', [
            'order' => $order
        ]);
    }


    #[Route('/validate', name: 'validate')]
    public function validate(Order $order, TokenGeneratorInterface $tokenGenerator, SessionInterface $sessionInterface)
    {

    }
}
