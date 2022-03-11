<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\ProductLine;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route('/order', name: 'order_')]
#[IsGranted('ROLE_USER')]
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

        foreach ($cart as $productId => $quantity) {
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

    /**
     * @param Order $order
     * @param OrderRepository $repo
     * @param SessionInterface $session
     * @return Response
     * @throws ApiErrorException
     * @throws Exception
     */
    #[Route('/validate/{order}', name: 'validate')]
    public function validate(Order $order, OrderRepository $repo, SessionInterface $session, TokenGeneratorInterface $token): Response
    {
        Stripe::setApiKey('sk_test_51Kc4G5JFPS7SAoqok30YmsBL9kWxLmlZrysv4AN0QrIkmqCSVdePlL7qelPuuK3A9br0dCcYhfkmlNKAF8IG1MtL00SnSOc0qu');

        $orderTab = [];

        $session->set("token_payment", $token->generateToken());

        foreach ($order->getProductLine() as $line) {
            $orderTab[] =
                [
                    "price_data" =>
                    [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $line->getProduct()->getName(),
                        ],
                        'unit_amount' => $line->getProduct()->getPrice() * 100,
                    ],
                    'quantity' => $line->getQuantity(),
                ];
        }
        $session->set("order_waiting", $order->getId());
        $session = Session::create([
            'line_items' => $orderTab,
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000/order/checkout_success/'.$token,
            'cancel_url' => 'http://localhost:8000/order/checkout-error'
        ]);


        return $this->redirect($session->url,303);
    }

    /**
     * @param EntityManagerInterface $em
     * @param SessionInterface $session
     * @param string $token
     * @param OrderRepository $repo
     * @return RedirectResponse
     */
    #[Route('/checkout_success/{token}', name: 'checkout_success')]
    public function success(EntityManagerInterface $em, SessionInterface $session, string $token, OrderRepository $repo){
        $tokenSession = $session->get("token_payment");
        if($token==$tokenSession){
            $order = $repo->find($session->get("order_waiting"));
            $order->setStatus(true);
            $em->persist($order);
            $em->flush();
            $session->remove("cart");
            $session->remove('token_payment');
            return $this->redirectToRoute("app_home", [
                "message" => "Votre commande a bien été passée"
            ]);
        }else{
            return $this->redirectToRoute("validate", [
                "order" => $repo->find($session->get("order_waiting"))
            ]);
        }
    }



    #[Route('/checkout-error', name: 'checkout_error')]
    public function error()
    {
        return $this->render("order/error.html.twig");
    }
}
