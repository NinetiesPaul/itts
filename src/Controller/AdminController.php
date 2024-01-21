<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Equipment;
use App\Entity\EquipmentType;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/admin/users', name: 'users', methods: ['GET'])]
    public function users(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $usersRepository = new UsersRepository($doctrine);
        $users = $usersRepository->findAllButMyself($user->getId());

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/users', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $userId = $request->get('id');

        $user = $doctrine->getRepository(Users::class)->findOneBy([ 'id' => $userId ]);

        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => [
                'message' => "User succesfully deleted"
            ]
        ]);
    }

    #[Route('/admin/users/new', name: 'users_form', methods: ['GET'])]
    public function newUser(ManagerRegistry $doctrine): Response
    {
        $departments = $doctrine->getRepository(Department::class)->findAll();

        return $this->render('admin/users_new.html.twig', [
            'departments' => $departments
        ]);
    }

    #[Route('/admin/users/{userId}', name: 'users_edit_form', methods: ['GET'])]
    public function userDetail(ManagerRegistry $doctrine, int $userId): Response
    {
        $departments = $doctrine->getRepository(Department::class)->findAll();

        $user = $doctrine->getRepository(Users::class)->findOneBy([ 'id' => $userId ]);

        return $this->render('admin/users_view.html.twig', [
            'departments' => $departments,
            'user' => $user
        ]);
    }

    #[Route('/admin/users', name: 'create_users', methods: ['POST'])]
    public function createUser(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $department = $doctrine->getRepository(Department::class)->find($request->get('department_id'));

        $newUser = new Users();
        $newUser->setName($request->get('name'));
        $newUser->setEmail($request->get('email'));
        $newUser->setPassword(password_hash($request->get('password'), PASSWORD_BCRYPT));
        $newUser->setRoles([$request->get('type_id')]);
        $newUser->setDepartment($department);

        $entityManager->persist($newUser);
        $entityManager->flush();

        $user = $this->getUser();

        $usersRepository = new UsersRepository($doctrine);
        $users = $usersRepository->findAllButMyself($user->getId());

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/users', name: 'update_users', methods: ['PUT'])]
    public function updateUser(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $department = $doctrine->getRepository(Department::class)->find($request->get('department_id'));

        $newUser = $doctrine->getRepository(Users::class)->find($request->get('id'));
        $newUser->setName($request->get('name'));
        $newUser->setEmail($request->get('email'));
        $newUser->setRoles([$request->get('type_id')]);
        $newUser->setDepartment($department);

        if ($request->get('password') !== '') {
            $newUser->setPassword(password_hash($request->get('password'), PASSWORD_BCRYPT));
        }

        $entityManager->persist($newUser);
        $entityManager->flush();

        $user = $this->getUser();

        $usersRepository = new UsersRepository($doctrine);
        $users = $usersRepository->findAllButMyself($user->getId());

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/equipments', name: 'equipments', methods: ['GET'])]
    public function equipments(ManagerRegistry $doctrine): Response
    {
        $equipments = $doctrine->getRepository(Equipment::class)->findAll();

        return $this->render('admin/equipment.html.twig', [
            'equipments' => $equipments
        ]);
        
    }

    #[Route('/admin/equipments', name: 'delete_equipment', methods: ['DELETE'])]
    public function deleteEquipments(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $equipmentId = $request->get('id');

        $equipment = $doctrine->getRepository(Equipment::class)->findOneBy([ 'id' => $equipmentId ]);

        $entityManager = $doctrine->getManager();
        $entityManager->remove($equipment);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => [
                'message' => "Equipment succesfully deleted"
            ]
        ]);
    }

    #[Route('/admin/equipments/new', name: 'equipments_form', methods: ['GET'])]
    public function newEquipments(ManagerRegistry $doctrine): Response
    {
        $equipmentTypes = $doctrine->getRepository(EquipmentType::class)->findAll();

        return $this->render('admin/equipment_new.html.twig', [
            'types' => $equipmentTypes,
        ]);
        
    }

    #[Route('/admin/equipments/{equipmentId}', name: 'equipments_edit_form', methods: ['GET'])]
    public function equipmentDetail(ManagerRegistry $doctrine, int $equipmentId): Response
    {
        $equipment = $doctrine->getRepository(Equipment::class)->findOneBy([ 'id' => $equipmentId ]);

        $equipmentTypes = $doctrine->getRepository(EquipmentType::class)->findAll();

        return $this->render('admin/equipment_view.html.twig', [
            'equipment' => $equipment,
            'types' => $equipmentTypes,
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

        $entityManager->persist($equipment);
        $entityManager->flush();

        $equipments = $doctrine->getRepository(Equipment::class)->findAll();

        return $this->render('admin/equipment.html.twig', [
            'equipments' => $equipments
        ]);
    }

    #[Route('/admin/equipments', name: 'update_equipment', methods: ['PUT'])]
    public function updateEquipment(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $equipmentType = $doctrine->getRepository(EquipmentType::class)->find($request->get('type_id'));

        $equipment = $doctrine->getRepository(Equipment::class)->find($request->get('id'));
        $equipment->setName($request->get('name'));
        $equipment->setSn($request->get('sn'));
        $equipment->setValue($request->get('value'));
        $equipment->setIsPart(($request->get('is_part')) ? true : false);
        $equipment->setEquipmentType($equipmentType);

        $entityManager->flush();

        $equipments = $doctrine->getRepository(Equipment::class)->findAll();

        return $this->render('admin/equipment.html.twig', [
            'equipments' => $equipments
        ]);
    }

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
