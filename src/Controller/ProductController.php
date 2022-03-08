<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name:"product_")]
class ProductController extends AbstractController
{
    #[Route('/show', name: 'show')]
    public function show(ProductRepository $products): Response
    {
        return $this->render('product/show.html.twig', [
            'products' => $products->findAll(),
        ]);
    }
}
