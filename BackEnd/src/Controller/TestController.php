<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TestRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Entity\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\Exception;
use DateTimeImmutable;
use App\Form\TestType;



class TestController extends AbstractController
{
    
    /**
     * @var TestRepository
     */
    private TestRepository $testRepository;

    /**
     * @var Serializer
     */
    private Serializer $serializer;

    function __construct(TestRepository $testRepository)
    {
        $this->testRepository = $testRepository;

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null,null,null,null,null, $defaultContext)];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/test', name: 'test_index', methods: ['GET','POST'])]
    public function index(): Response
    {
       $test = new Test();
       $test_form = $this->createForm(TestType::class, $test);
    
       return $this->render('test/index.html.twig', [
           'controller_name' => 'TestController',
           'test_form' => $test_form->createView(),
       ]);
    }

    #[Route('/test/table', name: 'test_table', methods: ['GET'])]
    public function table(): JsonResponse
    {
        $data = array();
        $tests = $this->testRepository->findAll();
        foreach ($tests as $test) {
            if ($test->getDeletedAt() == null) {
                $data[] = [
                    'id' => $test->getId(),
                    'Name' => $test->getName(),
                    'Price' => $test->getPrice(),
                    'File' => $test->getFile(),
                    'url_show' => $this->generateUrl('test_show', ['id' => $test->getId()]),
                    'url_edit' => $this->generateUrl('test_edit', ['id' => $test->getId()]),
                    'url_delete' => $this->generateUrl('test_delete', ['id' => $test->getId()]),
                ];
            }
        }
        $response = new JsonResponse();
        $response->setData($data);
        return $response;
    }

    #[Route('/test/new', name: 'test_new', methods: ['POST','GET'])]
    public function new(Request $request): JsonResponse
    {
        $test = new Test();
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);
        $response = new JsonResponse();
        try{
            if($form->isSubmitted() && $form->isValid()){
                $file = $form->get('File')->getData();
                if($file){
                    $this->saveFile($file, $test);
                }
                $test->setCreatedAt(new DateTimeImmutable());
                $this->testRepository->save($test, true);
                $response->setData(array("success" => "test created successfully"));        
            }else{
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

    #[Route('/test/show/{id}', name: 'test_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        $response = new JsonResponse();
        try{
            $test = $this->testRepository->find($id);
            if($test){
                $response = JsonResponse::fromJsonString($this->serializer->serialize($test, 'json'));
            }
            else{
                throw new Exception("Test not found");
            }
        }
        catch(Exception $e){
            $response->setData(array("error" => $e->getMessage()));
        }
        return $response;
    }

    #[Route('/test/edit/{id}', name: 'test_edit', methods: ['POST','GET'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $test = $this->testRepository->find($id);
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);
        $response = new JsonResponse();
        try{
            if($form->isSubmitted() && $form->isValid()){
                $test->setUpdatedAt(new DateTimeImmutable());
                $this->testRepository->save($test, true);
                $response->setData(array("success" => "Test updated successfully"));
            }else{
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

    #[Route('/test/delete/{id}', name: 'test_delete', methods: ['POST'])]
    public function delete(int $id): JsonResponse
    {
        $response = new JsonResponse();
        try{
            $test = $this->testRepository->find($id);
            if($test){
                $test->setDeletedAt(new DateTimeImmutable());
                $this->testRepository->save($test, true);
                $response->setData(array("success" => "Test deleted successfully"));
            }
            else{
                throw new Exception("Test not found");
            }
        }
        catch(Exception $e){
            $response->setData(array("error" => $e->getMessage()));
        }
        return $response;
    }

    public function saveFile($file,$test): void
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move(
            $this->getParameter('test_file_directory'),
            $fileName
        );
        $test->setFile($fileName);
    }
}
