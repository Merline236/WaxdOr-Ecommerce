<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/commandes', name: 'app_orders_')]
class OrdersController extends AbstractController
{
    #[Route('/ajout', name: 'add')]
    public function add(SessionInterface $session, ProductsRepository $productsRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);

       if($panier === []){
        $this->addFlash('message', 'Votre panier est vide');
        return $this->redirectToRoute('home');

       }

       //le panier n'est pas vide, on crée la commande
       $order = new Orders();

       //on remplit la commande
       $order->setUsers($this->getUser());
       $order->setReference(uniqid());


       //on parcourt le panier pour créer les details de commande
       foreach($panier as $item => $quantity){
        $orderDetails = new OrdersDetails();

        //on va chercher le produit
        $product = $productsRepository->find($item);

        $price = $product->getPrice();

        //on crée le detail de commande
        $orderDetails->setProducts($product);
        $orderDetails->setPrice($price);
        $orderDetails->setQuantity($quantity);


        $order->addOrdersDetail($orderDetails);


       }

       //on persiste et en flush
       $em->persist($order);
       $em->flush();

       $session->remove('panier');

        $this->addFlash('message', 'Commande créée avec succès');
        return $this->redirectToRoute('home');
    }
}
