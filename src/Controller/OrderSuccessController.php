<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    /**
     * @Route("/commande/merci/{stripeCheckoutId}", name="order_validate")
     */
    public function index(EntityManagerInterface $entityManager, $stripeCheckoutId, Cart $cart): Response
    {
        $order = $entityManager->getRepository(Order::class)->findOneByStripeCheckoutId($stripeCheckoutId);
    if(!$order || ($order->getUser() != $this->getUser())) {
        return $this->redirectToRoute('home');
    }
        if(!$order->getIsPaid()) {
            // vider la session "cart"
            $cart->delete();
            // passer ispaid a true
            $order->setIsPaid(1);
            $entityManager->flush();
        //envoyer un mail au client pour confirmer la commande

        }
        // afficher les qques info de la commande et le remercier

       return $this->render('order_success/index.html.twig', [
           'order'=>$order
       ]);
    }
}