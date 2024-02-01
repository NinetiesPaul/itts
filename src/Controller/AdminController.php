<?php

namespace App\Controller;

use App\Entity\CallNotes;
use App\Entity\Calls;
use App\Entity\Users;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Custom\Consts\StatusType as StatusTypeEnum;
use App\Entity\StatusType;

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

    #[Route('/admin/call/take', name: 'admin_take_call', methods: ['POST'])]
    public function takeCall(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $call = $doctrine->getRepository(Calls::class)->find($request->get('id'));

        $message = "Call answered successfully";
        $success = true;
        if (!$call->getAnsweredBy()) {
            $user = $doctrine->getRepository(Users::class)->find($this->getUser()->getId());
            $now = new DateTime();

            $call_note = new CallNotes();
            $call_note->setText("Hi! I'll be taking your call! I'm on my way to you now!");
            $call_note->setCallId($call);
            $call_note->setSentBy($user);
            $call_note->setSentOn($now);
            $entityManager->persist($call_note);

            $call->setAnsweredBy($user);
            $call->setAnsweredOn($now);
            $call->setStatus($doctrine->getRepository(StatusType::class)->findOneBy([ 'name' => StatusTypeEnum::TAKEN ]));
            $entityManager->flush();
        } else {
            $message = "Call answer failed: already taken by someone";
            $success = false;
        }

        return $this->json([
            'success' => $success,
            'data' => [
                'message' => $message,
                'payload' => []
            ]
        ]);
    }

    #[Route('/admin/call/close', name: 'admin_close_call', methods: ['POST'])]
    public function closeCall(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $call = $doctrine->getRepository(Calls::class)->find($request->get('id'));

        $user = $doctrine->getRepository(Users::class)->find($this->getUser()->getId());
        $now = new DateTime();

        $call_note = new CallNotes();
        $call_note->setText("Hi! It seems that this issue has been deemed as resolved by the technician, so it has been closed! Thank you!");
        $call_note->setCallId($call);
        $call_note->setSentBy($user);
        $call_note->setSentOn($now);
        $entityManager->persist($call_note);

        $call->setClosedBy($user);
        $call->setClosedOn($now);
        $call->setStatus($doctrine->getRepository(StatusType::class)->findOneBy([ 'name' => StatusTypeEnum::CLOSED ]));
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => [
                'message' => 'Call closed successfully',
                'payload' => []
            ]
        ]);
    }
}
