<?php

namespace App\Controller;


use App\Classes\Cart;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart, ProductRepository $repo): Response
    {
        $cartComplete = [];

        foreach ($cart->get() as $id => $quantity) {
            $cartComplete[]= [
                'product' => $repo->findOneById($id),
                'quantity' => $quantity
            ];
        }
        return $this->render('cart/index.html.twig', [
            'cart' => $cartComplete
        ] );
    }
    /**
     * @Route("/mon-panier/add/{id}", name="add_to_cart")
     */
    public function add( Cart $cart, $id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('cart');
    }
    /**
     * @Route("/mon-panier/remove/{id}", name="remove_product")
     */
    public function removeProduct( Cart $cart, $id): Response
    {
        $cart->remove($id);
        return $this->redirectToRoute('cart');
    }
    /**
     * @Route("/mon-panier/delete", name="delete_cart")
     */
    public function delete( Cart $cart): Response
    {
        $cart->delete();
        return $this->redirectToRoute('products');
    }

}