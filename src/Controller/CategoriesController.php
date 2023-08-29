<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



    #[Route('/categories', name: 'categories_')]
    class CategoriesController extends AbstractController
    
    {
        #[Route('/{slug}', name: 'list')]
        public function list(Categories $category,
         ProductsRepository $productsRepository, Request $request): Response
    {
        //on va chercher le numéro de page dans l'url
        $page = $request->query->getInt('page', 1);


        //la liste des produits de la categorie
        $products = $productsRepository->findProductsPaginated($page,
         $category->getSlug(), 4);

       
        return $this->render('categories/list.html.twig', compact('category', 'products'));

        //syntaxe alternative
        //return $this->render('categories/list.html.twig', [
            //'category'=>$category,
            //'products' => $products
        //]);
    }
}
