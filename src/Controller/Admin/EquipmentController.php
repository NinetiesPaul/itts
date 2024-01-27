<?php

namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Entity\Equipment;
use App\Entity\EquipmentType;
use App\Entity\Users;
use App\Repository\EquipmentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipmentController extends AdminController
{

    #[Route('/admin/equipments', name: 'equipments', methods: ['GET'])]
    public function equipments(ManagerRegistry $doctrine): Response
    {
        $parentEquipment = $this->formatEquipments($doctrine);

        return $this->render('admin/equipment.html.twig', [
            'equipments' => $parentEquipment
        ]);
        
    }

    #[Route('/admin/equipments', name: 'delete_equipment', methods: ['DELETE'])]
    public function deleteEquipments(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $equipmentId = $request->get('id');

        $equipment = $doctrine->getRepository(Equipment::class)->findOneBy([ 'id' => $equipmentId ]);

        $refreshPage = false;
        $equipmentsRepository = new EquipmentRepository($doctrine);
        $myParts = $equipmentsRepository->findAllMyParts($equipment->getId());
        foreach ($myParts as $part) {
            $part->setHasParent(null);
            $refreshPage = true;
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($equipment);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => [
                'message' => "Equipment succesfully deleted"
            ],
            'payload' => [ 'refresh' => $refreshPage ]
        ]);
    }

    #[Route('/admin/equipments/new', name: 'equipments_form', methods: ['GET'])]
    public function newEquipments(ManagerRegistry $doctrine): Response
    {
        $equipmentTypes = $doctrine->getRepository(EquipmentType::class)->findAll();

        $equipmentsRepository = new EquipmentRepository($doctrine);
        $parts = $equipmentsRepository->findAllNotParts();

        $users = $doctrine->getRepository(Users::class)->findAll();

        return $this->render('admin/equipment_new.html.twig', [
            'types' => $equipmentTypes,
            'parts' => $parts,
            'users' => $users
        ]);
        
    }

    #[Route('/admin/equipments/{equipmentId}', name: 'equipments_edit_form', methods: ['GET'])]
    public function equipmentDetail(ManagerRegistry $doctrine, int $equipmentId): Response
    {
        $equipment = $doctrine->getRepository(Equipment::class)->findOneBy([ 'id' => $equipmentId ]);

        $equipmentTypes = $doctrine->getRepository(EquipmentType::class)->findAll();

        $equipmentsRepository = new EquipmentRepository($doctrine);
        $partsAvailable = $equipmentsRepository->findAllNotParts();

        $myParts = [];
        if (!$equipment->getIsPart()) {
            $myParts = $equipmentsRepository->findAllMyParts($equipment->getId());
        }

        $users = $doctrine->getRepository(Users::class)->findAll();

        return $this->render('admin/equipment_view.html.twig', [
            'equipment' => $equipment,
            'types' => $equipmentTypes,
            'partsAvailable' => $partsAvailable,
            'myParts' => $myParts,
            'users' => $users
        ]);
        
    }

    #[Route('/admin/equipments', name: 'create_equipment', methods: ['POST'])]
    public function createEquipment(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $equipmentType = $doctrine->getRepository(EquipmentType::class)->find($request->get('type_id'));

        $equipment = new Equipment();
        $equipment->setName($request->get('name'));
        $equipment->setSn($request->get('sn'));
        $equipment->setValue($request->get('value'));
        $equipment->setIsPart(($request->get('is_part')) ? true : false);
        $equipment->setEquipmentType($equipmentType);

        if ($request->get('parent_id') !== "") {
            $parent = $doctrine->getRepository(Equipment::class)->find($request->get('parent_id'));
            $equipment->setHasParent($parent);
        }

        if ($request->get('user_id') !== "") {
            $user = $doctrine->getRepository(Users::class)->find($request->get('user_id'));
            $equipment->setUserId($user);
        }

        $entityManager->persist($equipment);
        $entityManager->flush();

        $equipments = $this->formatEquipments($doctrine);

        return $this->render('admin/equipment.html.twig', [
            'equipments' => $equipments
        ]);
    }

    #[Route('/admin/equipments/unsetparent', name: 'unset_equipment_parent', methods: ['PUT'])]
    public function unsetEquipmentParent(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $equipment = $doctrine->getRepository(Equipment::class)->find($request->get('id'));
        $equipment->setHasParent(null);

        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => [
                'message' => "Equipment successfully updated"
            ]
        ]);
    }

    #[Route('/admin/equipments', name: 'update_equipment', methods: ['PUT'])]
    public function updateEquipment(ManagerRegistry $doctrine, Request $request): Response
    {
        $equipmentType = $doctrine->getRepository(EquipmentType::class)->find($request->get('type_id'));

        $equipment = $doctrine->getRepository(Equipment::class)->find($request->get('id'));
        $equipment->setName($request->get('name'));
        $equipment->setSn($request->get('sn'));
        $equipment->setValue($request->get('value'));
        $equipment->setEquipmentType($equipmentType);
        
        $equipment->setIsPart(($request->get('is_part') == "on") ? true : false);

        if ($request->get('is_part') == "on") {
            if ($request->get('parent_id') !== "") {
                $parent = $doctrine->getRepository(Equipment::class)->find($request->get('parent_id'));
                $equipment->setHasParent($parent);
            } else {
                $equipment->setHasParent(null);
            }

            $equipmentsRepository = new EquipmentRepository($doctrine);
            $myParts = $equipmentsRepository->findAllMyParts($equipment->getId());

            foreach ($myParts as $part) {
                $part->setHasParent(null);
            }
        } else {
            $equipment->setHasParent(null);
        }

        if ($request->get('user_id') !== "") {
            $user = $doctrine->getRepository(Users::class)->find($request->get('user_id'));
            $equipment->setUserId($user);
        } else {
            $equipment->setUserId(null);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        $equipments = $this->formatEquipments($doctrine);

        return $this->render('admin/equipment.html.twig', [
            'equipments' => $equipments
        ]);
    }

    private function formatEquipments($doctrine)
    {
        $equipments = $doctrine->getRepository(Equipment::class)->findAll();

        $parentEquipment = [];
        $childEquipment = [];
        foreach ($equipments as $equipment) {
            if ($equipment->getIsPart()) {
                if($equipment->getHasParent()) {
                    $childEquipment[$equipment->getId()] = $equipment;
                } else {
                    $parentEquipment[] = $equipment;
                }
            } else {
                $parent = $equipment;
                $parent->parts = [];
                $parentEquipment[$equipment->getId()] = $parent;
            }
        }

        foreach ($childEquipment as $child) {
            $parentEquipment[$child->getHasParent()->getId()]->parts[] = $child;
        }

        return $parentEquipment;
    }
}
