<?php


namespace App\Classes;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $entityManager;
    public function __construct(SessionInterface $session,EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function add($id)
    {
        $cart =$this->session->get('cart',[]);

        if(!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        return $this->session->set('cart',$cart);
    }
    public function delete()
    {
        $this->session->remove('cart');
    }
    public function remove($id)
    {
        $cart = $this->session->get('cart',[]);
        unset($cart[$id]);
        return  $this->session->set('cart',$cart);

    }
    public function get()
    {
        return $this->session->get('cart');
    }

    public function getFull()
    {
        $cartComplete = [];


        if($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $product = $this->entityManager->getRepository(Product::class)->findOneById($id);
                if(!$product) { // si un user saisi une url avec id erroné on supprime le produit
                    // qui n'existe pas dans la bdd du panier
                    $this->remove($id);
                    continue; // permet de sortir de la boucle
                }
                $cartComplete[]= [
                    'product' =>  $product,
                    'quantity' => $quantity
                ];

            }

        }
        return $cartComplete;
    }
    public function decrease($id)
    {
        $cart =$this->session->get('cart',[]);
        if($cart[$id] >1){
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }
        return $this->session->set('cart',$cart);
    }
}