<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Repository\RoleRepository;
use App\Entity\Role;
use App\Form\RoleType;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;
use Symfony\Component\HttpFoundation\Request;



class RoleController extends AbstractController
{
    /**
     * @var RoleRepository
     */
    private RoleRepository $roleRepository;

    /**
     * @var Serializer
     */
    private Serializer $serializer;

    function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null,null,null,null,null, $defaultContext)];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/role', name: 'role_index', methods: ['GET','POST'])]
    public function index(): Response
    {
       $role = new Role();
       $role_form = $this->createForm(RoleType::class, $role);
    
       return $this->render('role/index.html.twig', [
           'controller_name' => 'RoleController',
           'role_form' => $role_form->createView(),
       ]);
    }

    #[Route('/role/table', name: 'role_table', methods: ['GET'])]
    public function table(Request $request): JsonResponse
    {
        $data = array();
        $roles = $this->roleRepository->findAll();
        foreach($roles as $role){
            if($role->getDeletedAt() == null){
                $data[] = array(
                    'id' => $role->getId(),
                    'Name' => $role->getName(),
                    "url_show" => $this->generateUrl('role_show', ['id' => $role->getId()]),
                    "url_edit" => $this->generateUrl('role_edit', ['id' => $role->getId()]),
                    "url_delete" => $this->generateUrl('role_delete', ['id' => $role->getId()]),
                );
            }
        }
        $response = new JsonResponse();
        $response->setData($data);
        return $response;

    }

    #[Route('/role/new', name: 'role_new', methods: ['POST','GET'])]
    public function new(Request $request): JsonResponse
    {
        $role = new Role();
        $role_form = $this->createForm(RoleType::class, $role);
        $role_form->handleRequest($request);
        $response = new JsonResponse();
        try{
            if($role_form->isSubmitted() && $role_form->isValid()){
            $role->setCreatedAt(new DateTimeImmutable());
            $this->roleRepository->save($role, true);
            $response->setData(array("success" => "Role created successfully"));
        }
            else{
                $errorMessages = array();
                        foreach ($form->getErrors(true) as $error) {
                            $errorMessages[] = $error->getMessage();
                        }

                        $errorMessage = implode(" ", $errorMessages);
                        throw new Exception($errorMessage);
            }
        }
        catch (Exception $e){
            $response->setData(array("error" => $e->getMessage()));
        }
        return $response;
    }

    #[Route('/role/show/{id}', name: 'role_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $response = new JsonResponse();
        try{
            $role = $this->roleRepository->find($id);
            if($role){
                $response = JsonResponse::fromJsonString($this->serializer->serialize($role, 'json'));                    
            }
            else{
                throw new Exception("Role not found");
            }
        }
        catch(Exception $e){
            $response->setData(array("error" => $e->getMessage()));
        }
        return $response;
    }

    #[Route('/role/edit/{id}', name: 'role_edit', methods: ['GET','POST'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $role = $this->roleRepository->find($id);
        $role_form = $this->createForm(RoleType::class, $role);
        $role_form->handleRequest($request);
        $response = new JsonResponse();
        try{
            if($role_form->isSubmitted() && $role_form->isValid()){
                $role->setUpdatedAt(new DateTimeImmutable());
                $this->roleRepository->save($role, true);
                $response->setData(array("success" => "Role updated successfully"));
            }
            else{
                $errorMessages = array();
                foreach ($form->getErrors(true) as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                $errorMessage = implode(" ", $errorMessages);
                throw new Exception($errorMessage);
            }
        }
        catch (Exception $e){
            $response->setData(array("error" => $e->getMessage()));
        }
        return $response;
    }

    #[Route('/role/delete/{id}', name: 'role_delete', methods: ['POST'])]
    public function delete(int $id): JsonResponse
    {
        $role = $this->roleRepository->find($id);
        $response = new JsonResponse();
        try{
            if($role){
                $role->setDeletedAt(new DateTimeImmutable());
                $this->roleRepository->save($role, true);
                $response->setData(array("success" => "Role deleted successfully"));
            }
            else{
                throw new Exception("Role not found");
            }
        }
        catch(Exception $e){
            $response->setData(array("error" => $e->getMessage()));
        }
        return $response;
    }

}
