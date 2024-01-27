<?php

namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Entity\EquipmentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipmentTypeController extends AdminController
{



    #[Route('/admin/equipment_type', name: 'equipment_type', methods: ['GET'])]
    public function equipmentType(ManagerRegistry $doctrine): Response
    {
        $equipmentTypes = $doctrine->getRepository(EquipmentType::class)->findAll();
        return $this->render('admin/equipment_type.html.twig', [
            'types' => $equipmentTypes,
        ]);
    }

    #[Route('/admin/equipment_type', name: 'delete_equipment_type', methods: ['DELETE'])]
    public function deleteEquipmentType(ManagerRegistry $doctrine, Request $request): Response
    {
        $equipmentTypeId = $request->get('id');

        $equipmentType = $doctrine->getRepository(EquipmentType::class)->findOneBy([ 'id' => $equipmentTypeId ]);

        $entityManager = $doctrine->getManager();
        $entityManager->remove($equipmentType);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => [
                'message' => "Equipment Type succesfully deleted"
            ]
        ]);
    }

    #[Route('/admin/equipment_type/new', name: 'equipment_type_form', methods: ['GET'])]
    public function newEquipmentType(): Response
    {
        return $this->render('admin/equipment_type_new.html.twig', []);
    }

    #[Route('/admin/equipment_type', name: 'create_equipment_type', methods: ['POST'])]
    public function createEquipmentType(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $equipmentType = new EquipmentType();
        $equipmentType->setName($request->get('name'));

        $entityManager->persist($equipmentType);
        $entityManager->flush();

        $equipmentTypes = $doctrine->getRepository(EquipmentType::class)->findAll();
        return $this->render('admin/equipment_type.html.twig', [
            'types' => $equipmentTypes,
        ]);
    }
}
