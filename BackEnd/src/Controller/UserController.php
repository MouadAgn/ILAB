<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use App\Repository\UserRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Form\UserType;


class UserController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $jwtManager;
    private $roleRepository;
    private $userRepository;
    private $mailer;


    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        RoleRepository $roleRepository,
        UserRepository $userRepository,
        MailerInterface $mailer

    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;

    }
    
    #[Route('/users', name: 'users_index', methods: ['GET','POST'])]
    public function index(): Response
    {
        $user = new User();
        $user_form = $this->createForm(UserType::class, $user);
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'user_form' => $user_form->createView()
        ]);
    }

    #[Route('/api/users', name: 'api_list_users', methods: ['GET'])]
    public function listUsers(): JsonResponse
    {
        $users = $this->userRepository->findBy(['deleted_at' => null]);

        $formattedUsers = array_map(function($user) {
            return [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()->getName(),
                'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $user->getUpdatedAt() ? $user->getUpdatedAt()->format('Y-m-d H:i:s') : null,
            ];
        }, $users);

        return $this->json($formattedUsers);
    }


    #[Route('/api/', name: 'api_register', methods: ['GET','POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
    
        // Gestion du rôle
        $roleName = strtoupper($data['role']);
        $role = $this->roleRepository->findOneBy(['Name' => $roleName]);
        if (!$role) {
            return $this->json(['message' => 'Invalid role: ' . $roleName], 400);
        }
        $user->setRole($role);
    
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    
        $token = $this->jwtManager->create($user);
        $user->setToken($token);
    
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Envoyer l'e-mail de confirmation
    
        return $this->json([
            'message' => 'User registered successfully',
            'token' => $token
        ]);
    }

    

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['Email' => $data['email']]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $this->jwtManager->create($user);
        $user->setToken($token);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Login successful',
            'token' => $token
        ]);
    }

    // ROUTE TO UPDATE A USER INFORMATIONS

    #[Route('/api/users/update/{id}', name: 'api_update_user', methods: ['PUT'])]
    public function updateUser(Request $request, int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $isUpdated = false;

        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
            $isUpdated = true;
        }
        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
            $isUpdated = true;
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
            $isUpdated = true;
        }
        if (isset($data['password'])) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            $isUpdated = true;
        }
        if (isset($data['role'])) {
            $roleName = strtoupper($data['role']);
            $role = $this->roleRepository->findOneBy(['Name' => $roleName]);
            if (!$role) {
                return $this->json(['message' => 'Invalid role: ' . $roleName], 400);
            }
            $user->setRole($role);
            $isUpdated = true;
        }

        if ($isUpdated) {
            $user->setUpdatedAt(new \DateTime());
        } else {
            $user->setUpdatedAt(null);
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'User updated successfully',
            'user' => [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()->getName(),
                'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $user->getUpdatedAt() ? $user->getUpdatedAt()->format('Y-m-d H:i:s') : null,
            ]
        ]);
    }

    // SOFT DELETE ROUTE 

    #[Route('/api/users/delete/{id}', name: 'api_soft_delete_user', methods: ['DELETE'])]
    public function softDeleteUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }

        $user->setDeletedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $this->json([
            'message' => 'User soft deleted successfully',
            'user' => [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
            ]
        ]);
    }

   


    #[Route('/api/roles', name: 'api_roles', methods: ['GET'])]
    public function getRoles(): JsonResponse
    {
        $roles = $this->roleRepository->findAll();
        $formattedRoles = array_map(function($role) {
            return [
                'id' => $role->getId(),
                'name' => $role->getName()
            ];
        }, $roles);
        
        return $this->json($formattedRoles);
    }

   
}