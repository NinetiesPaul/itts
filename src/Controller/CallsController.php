<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CallsController extends AbstractController
{
    #[Route('/calls', name: 'app_calls')]
    public function index(): Response
    {
        return $this->render('calls/index.html.twig', [
            'controller_name' => 'CallsController',
        ]);
    }
}
