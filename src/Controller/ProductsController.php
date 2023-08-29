<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/produits', name: 'products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('products/index.html.twig', [
            'categories' => $categoriesRepository->findBy([], ['categoryOrder' =>'asc'])
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Products $product ): Response
    {
        //dd($product->getDescription());
    return $this->render('products/details.html.twig', compact('product'));
    //compact pour envoyer notre produit dans notre view
    }

}
