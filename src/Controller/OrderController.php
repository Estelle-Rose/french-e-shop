<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart): Response
    {
        $user = $this->getUser();

        if(!$user->getAddresses()->getValues()) { // getValues permet de récupérer les data de getAdresses
            return $this->redirectToRoute('add_address');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser() // on récupère le user connecté pour les adresses de ce dernier
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }
    /**
     * @Route("/commande/recapitulatif", name="order-summary", methods={"POST"})
     */
    public function add(Cart $cart, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser() // on récupère le user connecté pour les adresses de ce dernier
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $carrier = $form->get('carriers')->getData();
            $address = $form->get('addresses')->getData();
            $delivery_content = $address->getFirstname().' '.$address->getLastname();
            if($address->getCompany()) {
                $delivery_content .= '<br/>'.$address->getCompany();
            }
            $delivery_content .= '<br/>'.$address->getAddress().' '.$address->getPostcode();
            $delivery_content .= '<br/>'.$address->getCity().' '.$address->getCountry();
            $delivery_content .= '<br/>'.$address->getPhone();

           //enregistrer ma commande Order
            $order = new Order();
            //on genere un id
            $ref = $date->format('dmY').'-'.uniqid();
            $order->setReference($ref);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState(0);
            $entityManager->persist($order);

            // enregistrer mes produits OrderDetails
            foreach ($cart->getFull() as $product) {
            $orderDetails = new OrderDetails();
            $orderDetails->setMyOrder($order);
            $orderDetails->setProduct($product['product']->getName());
            $orderDetails->setQuantity($product['quantity']);
            $orderDetails->setPrice($product['product']->getPrice());
            $orderDetails->setTotal($product['product']->getPrice()*$product['quantity']);
            $entityManager->persist($orderDetails);

            }
            $entityManager->flush();




           return $this->render('order/addOrder.html.twig', [
                'cart' => $cart->getFull(),
               'address' => $delivery_content,
               'order' => $order,
               'carrier' => $carrier,
               'reference' => $order->getReference()
            ]);

        }
        return $this->redirectToRoute('cart');

    }
}