<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/home', name: 'user_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('users/home.html.twig', [
        ]);
    }

    #[Route('/user/call/new', name: 'user_create_call_form', methods: ['GET'])]
    public function createCallForm(): Response
    {
        return $this->render('users/create_call.html.twig', [
        ]);
    }

    #[Route('/user/call', name: 'user_create_call', methods: ['POST'])]
    public function createCall(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => [
                'message' => "Call placed"
            ]
        ]);
    }
}
