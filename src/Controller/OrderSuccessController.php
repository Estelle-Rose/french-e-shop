<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\MailJet;
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
        $notification = null;
        $order = $entityManager->getRepository(Order::class)->findOneByStripeCheckoutId($stripeCheckoutId);
    if(!$order || ($order->getUser() != $this->getUser())) {
        return $this->redirectToRoute('home');
    }
        if($order->getState()== 0) {
            // vider la session "cart"
            $cart->delete();
            // passer ispaid a true
            $order->setState(1);
            $entityManager->flush();
        //envoyer un mail au client pour confirmer la commande
            $notification = 'Votre commande est bien enregistrée. Vous pouvez vous connecter pour accéder à votre compte.';
            $mail = new MailJet();
            $content = "Bonjour ".$order->getUser()->getFullname()."<br/><p>Votre commande".$order->getReference()." est bien enregistrée et va être préparéé pour l'envoi.</p><p>Merci et à bientôt !</p>";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFullname(),'Votre commande sur la boutique française', $content);
        }
        // afficher les qques info de la commande et le remercier

       return $this->render('order_success/index.html.twig', [
           'order'=>$order,
           'notification' => $notification
       ]);
    }
}