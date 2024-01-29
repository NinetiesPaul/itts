<?php

namespace App\Controller;

use App\Entity\Calls;
use Doctrine\Persistence\ManagerRegistry;
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

    #[Route('/admin/calls', name: 'admin_calls', methods: ['GET'])]
    public function calls(ManagerRegistry $doctrine): Response
    {
        $calls = $doctrine->getRepository(Calls::class)->findAll();

        foreach ($calls as &$call) {
            $call->title = substr($call->getNotes()->getValues()[0]->getText(), 0, 40) . "...";
        }

        return $this->render('admin/calls.html.twig', [
            'calls' => $calls,
        ]);
    }

    #[Route('/admin/call/{callId}', name: 'admin_call_view', methods: ['GET'])]
    public function viewCall(ManagerRegistry $doctrine, int $callId): Response
    {
        $call = $doctrine->getRepository(Calls::class)->findOneBy([ 'id' => $callId ]);
        
        return $this->render('admin/call_view.html.twig', [
            'call' => $call,
        ]);
    }
}
