<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Form\PatientType;
use App\Repository\PatientRepository;
use DateTimeImmutable;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;


class PatientController extends AbstractController
{

     /**
     * @var Serializer
     */
    private Serializer $serializer;

    /**
     * @var PatientRepository;
     */
    private PatientRepository $patientRepository;

    private UserPasswordHasherInterface $hasher;

    function __construct(PatientRepository $patientRepository)
    {
        $this->patientRepository = $patientRepository;

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null,null,null,null,null, $defaultContext)];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/patient', name: 'patient_index', methods: ['GET','POST'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
       $patient = new Patient();
       $patient_form = $this->createForm(PatientType::class, $patient);
       $defaultContext = [
        AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
            return $object->getId();
        },
       ];
       return $this-> render('patient/index.html.twig', [
        'controller_name' => 'PatientController',
           'patient_form' => $patient_form->createView(),
       ]);

        
    }
    #[Route('/patient/table', name: 'patient_table', methods: ['GET'])]
    public function table(Request $request): JsonResponse
    {
        $data = array();
        $patients = $this->patientRepository->findAll();
        foreach($patients as $patient){
            if($patient->getDeletedAt() == null){
                $data[] = array(
                    'id' => $patient->getId(),
                    'id_number' => $patient->getIdNumber(),
                    'FirstName' => $patient->getFirstName(),
                    'LastName' => $patient->getLastName(),
                    'Email' => $patient->getEmail(),
                    'DateOfBirth' => $patient->getDateOfBirth()->format('Y-m-d'),
                    'Gender' => $patient->getGender(),
                    'PhoneNumber' => $patient->getPhoneNumber(),
                    "url_show" => $this->generateUrl('patient_show', ['id' => $patient->getId()]),
                    "url_edit" => $this->generateUrl('patient_edit', ['id' => $patient->getId()]),
                    "url_delete" => $this->generateUrl('patient_delete', ['id' => $patient->getId()]),
                );
            }
        }
        $response = new JsonResponse();
        $response->setData($data);
        return $response;
    }

    #[Route('/patient/new', name: 'patient_new', methods: ['GET', 'POST'])]
    public function new(Request $request): JsonResponse
    {
        $patient = new Patient();
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);
        $response = new JsonResponse();
    try{
        if ($form->isSubmitted() && $form->isValid()) {
            $patient->setCreatedAt(new DateTimeImmutable());
            $this->patientRepository->save($patient,true);
            $response->setData(array("succes" => "The patient has been created"));
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


    #[Route('/patient/show/{id}', name: 'patient_show', methods: ['GET'])]
    public function show(int $id,Patient $patient): JsonResponse
    {
        $response = new JsonResponse();
        try{
            $patient = $this->patientRepository->find($id);
            if($patient){
                $response = JsonResponse::fromJsonString($this->serializer->serialize($patient, 'json'));
            }else{
                throw new Exception("We couldn't find the patient");
            }
        }
        catch (Exception $e){
            $response->setData(array("error" => $e->getMessage()));
        }
        return $response;
    }

    #[Route('/patient/edit/{id}', name: 'patient_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        $patient = $this->patientRepository->find($id);
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);
        $response = new JsonResponse();
        try{
            if ($form->isSubmitted() && $form->isValid()) {
                $patient->setUpdatedAt(new DateTimeImmutable());
                $this->patientRepository->save($patient);
                $response->setData(array("succes" => "The patient has been updated"));
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

    #[Route('/patient/delete/{id}', name: 'patient_delete', methods: ['POST'])]
    public function delete(Request $request,int $id): JsonResponse
    {
        $response = new JsonResponse();
        try{
            $patient = $this->patientRepository->find($id);
            if($patient){
                $patient->setDeletedAt(new DateTimeImmutable());
                $this->patientRepository->save($patient, true);
                $response->setData(array("succes" => "The patient has been deleted"));
            }else{
                throw new Exception("We couldn't find the patient");
            }
        }
        catch (Exception $e){
            $response->setData(array("error" => $e->getMessage()));
        }
        return $response;
    }
}

