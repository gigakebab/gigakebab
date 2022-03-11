<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    /**
     * @return Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    #[Route('/checkout', name: 'app_checkout')]
    public function index(): Response
    {
        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'T-shirt',
                    ],
                    'unit_amount' => 2000,
                ],
                'quantity' => 1,
            ],
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Confiture',
                        ],
                        'unit_amount' => 3000,
                    ],
                    'quantity' => 2,
                ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000/checkout_success',
            'cancel_url' => 'http://localhost:8000/checkout_error'
        ]);


        return $this->redirect($session->url,303);
    }
}
