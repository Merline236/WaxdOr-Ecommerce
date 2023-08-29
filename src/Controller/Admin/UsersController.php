<?php

namespace App\Controller\Admin;

use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin/utilisateurs', name: 'admin_users_')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $usersRepository): Response
    {
    $users = $usersRepository->findBy([], ['firstname' => 'asc']);
    return $this->render('admin/users/index.html.twig', compact('users'));
    }

}  