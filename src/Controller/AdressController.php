<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AdressType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdressController extends AbstractController
{
    #[Route('/adress', name: 'app_adress')]
    public function index(): Response
    {
        return $this->render('adress/index.html.twig', [
            'controller_name' => 'AdressController',
        ]);
    }

    #[Route('/address/add', name: 'app_adress_add')]
    public function add(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $addresses = $userRepo->getAddresses();

        $address = new Address();

        $form = $this->createForm(AdressType::class, $address)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $address = $form->getData();

            $em->persist($address);

            $em->flush();

            $this->addFlash(
                'success',
                'L\'adresse' . $address . 'a bien été ajoutée.'
            );

            return $this->redirectToRoute("app_home");
        }

        return $this->render('address/add.html.twig', [
            "addresses" => $addresses,
        ]);
    }
}
