<?php

namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Entity\Department;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DepartmentsController extends AdminController
{
    #[Route('/admin/departments', name: 'departments', methods: ['GET'])]
    public function departments(ManagerRegistry $doctrine): Response
    {
        $departments = $doctrine->getRepository(Department::class)->findAll();

        return $this->render('admin/departments.html.twig', [
            'departments' => $departments,
        ]);
    }

    #[Route('/admin/departments/new', name: 'department_form', methods: ['GET'])]
    public function newDepartment(): Response
    {
        return $this->render('admin/departments_new.html.twig', []);
    }

    #[Route('/admin/departments', name: 'create_department', methods: ['POST'])]
    public function createDepartments(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $department = new Department();
        $department->setTitle($request->get('name'));

        $entityManager->persist($department);

        $entityManager->flush();

        $departments = $doctrine->getRepository(Department::class)->findAll();
        return $this->render('admin/departments.html.twig', [
            'departments' => $departments,
        ]);
    }

    #[Route('/admin/departments', name: 'delete_department', methods: ['DELETE'])]
    public function deleteDepartment(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $departmentId = $request->get('id');

        $department = $doctrine->getRepository(Department::class)->findOneBy([ 'id' => $departmentId ]);

        $entityManager = $doctrine->getManager();
        $entityManager->remove($department);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => [
                'message' => "Department succesfully deleted"
            ]
        ]);
    }
}
