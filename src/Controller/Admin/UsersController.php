<?php

namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Entity\Department;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AdminController
{
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
}
