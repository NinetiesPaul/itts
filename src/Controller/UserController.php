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
use App\Entity\StatusType;
use App\Custom\Consts\StatusType as StatusTypeEnum;

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
    public function createCall(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $createdBy = $doctrine->getRepository(Users::class)->find($request->get('who')['id']);
        $createdOn = new DateTime($request->get('when')['date']);

        $call = new Calls();
        $call->setOpenedBy($createdBy);
        $call->setOpenedOn($createdOn);
        $call->setStatus($doctrine->getRepository(StatusType::class)->findOneBy([ 'name' => StatusTypeEnum::NEW ]));

        $entityManager->persist($call);
        $entityManager->flush();

        $call_note = new CallNotes();
        $call_note->setText($request->get('where')['description']);
        $call_note->setCallId($call);
        $call_note->setSentBy($createdBy);
        $call_note->setSentOn($createdOn);

        $entityManager->persist($call_note);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => [
                'message' => "Call placed successfully",
                'payload' => [ 'id' => $call->getId() ]
            ]
        ]);
    }

    #[Route('/user/calls', name: 'user_calls', methods: ['GET'])]
    public function viewCalls(ManagerRegistry $doctrine): Response
    {
        $calls = $doctrine->getRepository(Calls::class)->findBy([ 'opened_by' => $this->getUser()->getId() ]);

        foreach ($calls as &$call) {
            $call->title = substr($call->getNotes()->getValues()[0]->getText(), 0, 40) . "...";
        }

        return $this->render('users/calls.html.twig', [
            'calls' => $calls
        ]);
    }

    #[Route('/user/call/{callId}', name: 'user_call_view', methods: ['GET'])]
    public function viewCall(ManagerRegistry $doctrine, int $callId): Response
    {
        $call = $doctrine->getRepository(Calls::class)->findOneBy([ 'id' => $callId ]);

        return $this->render('users/call_view.html.twig', [
            'call' => $call
        ]);
    }
}
