<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AdressType;
use App\Repository\AddressRepository;
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

    #[Route('/adress/add', name: 'app_adress_add')]
    public function add(Request $request, EntityManagerInterface $em, AddressRepository $addressRepository): Response
    {
        $address = new Address();

        $form = $this->createForm(AdressType::class, $address)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            $em->persist($address);

            $em->flush();

            $this->addFlash(
                'success',
                'L\'adresse' . $address->getName() . 'a bien été ajoutée.'
            );

            return $this->redirectToRoute("app_home");
        }

        return $this->renderForm('adress/add.html.twig', [
            "addresses" => $addressRepository->findBy(['user' => $this->getUser()]),
            "form" => $form
        ]);
    }
}
