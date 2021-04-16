<?php

namespace App\Controller;

use App\Classes\Search;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(ProductRepository $repository, Request $request): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid()) {
           $products = $repository->findBySearch($search);
        }
        else {
            $products = $repository->findAll();

        }
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/produit/{slug}", name="product")
     */
    public function getOneProduct(ProductRepository $repository, $slug): Response
    {
        $product = $repository->findOneBySlug($slug);
        if(!$product) {
            return $this->redirectToRoute('products');
        }
        return $this->render('product/product.html.twig', [
            'product' => $product
        ]);
    }
}