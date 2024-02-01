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

class CallsController extends AbstractController
{
    #[Route('/call/note', name: 'send_call_note', methods: ['POST'])]
    public function makeNoteOnCall(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $call = $doctrine->getRepository(Calls::class)->find($request->get('id'));

        $user = $doctrine->getRepository(Users::class)->find($this->getUser()->getId());
        $now = new DateTime();

        $call_note = new CallNotes();
        $call_note->setText($request->get('text'));
        $call_note->setCallId($call);
        $call_note->setSentBy($user);
        $call_note->setSentOn($now);
        $entityManager->persist($call_note);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => [
                'message' => 'Call note sent successfully',
                'payload' => []
            ]
        ]);
    }
}
