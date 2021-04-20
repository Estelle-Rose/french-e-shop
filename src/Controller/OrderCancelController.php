<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{
    /**
     * @Route("/commande/erreur/{stripeCheckoutId}", name="order_cancel")
     */
    public function index(EntityManagerInterface $entityManager, $stripeCheckoutId): Response
    {
        $order = $entityManager->getRepository(Order::class)->findOneByStripeCheckoutId($stripeCheckoutId);
       //envoyer un mail d'échec de paiment éventuellement
        // afficher les qques info de la commande et le remercier
        return $this->render('order_cancel/index.html.twig', [
            'order' => $order,
        ]);

    }
}