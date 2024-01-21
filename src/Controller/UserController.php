<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/home', name: 'user_home')]
    public function index(): Response
    {
        return $this->render('users/home.html.twig', [
        ]);
    }
}
