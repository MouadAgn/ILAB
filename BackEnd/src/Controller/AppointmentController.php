<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Repository\AppointmentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Exception;



class AppointmentController extends AbstractController
{
   /**
    * @var Serializer
    */
    private Serializer $serializer;

    /**
     * @var AppointmentRepository;
     */
    private AppointmentRepository $appointmentRepository;

    function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null,null,null,null,null, $defaultContext)];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/appointment', name: 'appointment_index', methods: ['GET','POST'])]
    public function index(): Response
    {
        $appointment = new Appointment();
        $appointment_form = $this->createForm(AppointmentType::class, $appointment);
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        return $this->render('appointment/index.html.twig', [
            'controller_name' => 'AppointmentController',
            'appointment_form' => $appointment_form->createView(),
        ]);
    }

    #[Route('/appointment/table', name: 'appointment_table', methods: ['GET'])]
    public function table(Request $request): JsonResponse
    {
        $data = array();
        $appointments = $this->appointmentRepository->findAll();
        foreach ($appointments as $appointment) {
            if ($appointment->getDeletedAt() == null) {
                $data[] = [
                    'id' => $appointment->getId(),
                    'DateTimeOfAppointment' => $appointment->getDateTimeOfAppointment()->format('Y-m-d H:i:s'),
                    'TypeOfTest' => $appointment->getTypeOfTest()->getName(),
                    'Confirmation' => $appointment->getConfirmation(),
                    'Doctor' => $appointment->getDoctor()->getfirstName(). ' ' . $appointment->getDoctor()->getLastName(),
                    'Patient' => $appointment->getPatient()->getFirstName() . ' ' . $appointment->getPatient()->getLastName(),
                    "url_show" => $this->generateUrl('appointment_show', ['id' => $appointment->getId()]),
                    "url_edit" => $this->generateUrl('appointment_edit', ['id' => $appointment->getId()]),
                    "url_delete" => $this->generateUrl('appointment_delete', ['id' => $appointment->getId()]),
                ];
            }
        }
        $response = new JsonResponse();
        $response->setData($data);
        return $response;
    }

    #[Route('/appointment/new', name: 'appointment_new', methods: ['GET', 'POST'])]
        public function new(Request $request): JsonResponse
        {
            $appointment = new Appointment();
            $form = $this->createForm(AppointmentType::class, $appointment);
            $form->handleRequest($request);
            $response = new JsonResponse();

            try{
                if ($form->isSubmitted() && $form->isValid()) {
                    $appointment->setCreatedAt(new \DateTimeImmutable());
                    $this->appointmentRepository->save($appointment, true);
                    $response->setData(array('status' => 'Appointment created!'));
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

    #[Route('/appointment/show/{id}', name: 'appointment_show', methods: ['GET'])]
    public function show(int $id,): JsonResponse
    {
        $response = new JsonResponse();
        try{
            $appointment = $this->appointmentRepository->find($id);
            if ($appointment) {
                $response = JsonResponse::fromJsonString($this->serializer->serialize($appointment, 'json'));
            }
            else{
               throw new Exception("SORRY! Appointment not found!");
            }
        }
        catch (Exception $e){
            $response->setData(array("error" => $e->getMessage()));
        }
        return $response;
    }

    #[Route('/appointment/edit/{id}', name: 'appointment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        $appointment = $this->appointmentRepository->find($id);
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);
        $response = new JsonResponse();
        try{
            if ($form->isSubmitted() && $form->isValid()) {
                $appointment->setUpdatedAt(new \DateTimeImmutable());
                $this->appointmentRepository->save($appointment, true);
                $response->setData(array('succes' => 'Appointment updated!'));
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

    #[Route('/appointment/delete/{id}', name: 'appointment_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        $response = new JsonResponse();
        try{
            $appointment = $this->appointmentRepository->find($id);
            if ($appointment) {
                $appointment->setDeletedAt(new \DateTimeImmutable());
                $this->appointmentRepository->save($appointment, true);
                $response->setData(array('succes' => 'Appointment deleted!'));
            }
            else{
                throw new Exception("SORRY! Appointment not found!");
            }
        }
        catch (Exception $e){
            $response->setData(array("error" => $e->getMessage()));
        }
        return $response;
    }
}
