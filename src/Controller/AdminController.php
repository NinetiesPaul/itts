<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/home', name: 'admin_home')]
    public function index(): Response
    {

        return $this->render('admin/home.html.twig', [
            'new_calls' => 1,
            'on_going_calls' => 3,
            'equipments' => 14,
            'departments' => 6,
            'users' => 36
        ]);
    }
}
