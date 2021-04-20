<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Address;

use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    /**
     * @Route("/mon-compte/adresses", name="address")
     */
    public function index(): Response
    {

        return $this->render('account/address.html.twig', [

        ]);
    }
    /**
     * @Route("/mon-compte/ajouter-une-adresse", name="add_address")
     */
    public function add(Cart $cart,Request $request, EntityManagerInterface $entityManager): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $entityManager->persist($address);

            $entityManager->flush();
            if($cart->get()) {
                return $this->redirectToRoute('order');

            }
            return $this->redirectToRoute('address');
        }
        return $this->render('account/addressForm.html.twig', [
            'form' => $form->createView(),

        ]);
    }
    /**
     * @Route("/mon-compte/modifier-une-adresse/{id}", name="update_address")
     */
    public function update(Request $request, EntityManagerInterface $entityManager, AddressRepository $repo, $id): Response
    {
        $address = $repo->findOneById($id);
        if(!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('address');
        }
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('address');
        }
        return $this->render('account/addressForm.html.twig', [
            'form' => $form->createView(),

        ]);
    }
    /**
     * @Route("/mon-compte/supprimer-une-adresse/{id}", name="delete_address")
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, AddressRepository $repo, $id): Response
    {
        $address = $repo->findOneById($id);
        if(!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('address');
        }
        else {
            $entityManager->remove($address);
            $entityManager->flush();
            return $this->redirectToRoute('address');
        }

    }


}