<?php
namespace AlexNzr\BitUmcIntegration\Controller;


use AlexNzr\BitUmcIntegration\Service\OneCReader;
use AlexNzr\BitUmcIntegration\Service\OneCWriter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/umc-api")
 */
class OneCController
{
    private OneCReader $reader;
    private OneCWriter $writer;

    public function __construct(/*OneCReader $reader, OneCWriter $writer*/)
    {
        //$this->reader = $reader;
        //$this->writer = $writer;
        $this->reader = new OneCReader();
        $this->writer = new OneCWriter();
    }

    /**
     * @Route("/appointment/getData", name="appointment.getData", methods={"POST", "GET"})
     */
    public function getAppointmentData(): JsonResponse
    {
        $response = $this->reader->getAppointmentData();
        return JsonResponse::fromJsonString($response);
    }

    /**
     * @Route("/clinic/list", name="clinic.list", methods={"POST", "GET"})
     */
    public function getClinicsList(): JsonResponse
    {
        $response = $this->reader->getClinicsList();
        return JsonResponse::fromJsonString($response);
    }

    /**
     * @Route("/employee/list", name="employee.list", methods={"POST", "GET"})
     */
    public function getEmployeesList($params): JsonResponse
    {
        $response = $this->reader->getEmployeesList($params);
        return JsonResponse::fromJsonString($response);
    }

    /**
     * @Route("/nomenclature/list", name="nomenclature.list", methods={"POST", "GET"})
     */
    public function getNomenclatureList($params): JsonResponse
    {
        $response = $this->reader->getNomenclatureList($params);
        return JsonResponse::fromJsonString($response);
    }

    /**
     * @Route("/schedule/get", name="schedule.get", methods={"POST", "GET"})
     */
    public function getSchedule(): JsonResponse
    {
        $response = $this->reader->getSchedule();
        return JsonResponse::fromJsonString($response);
    }

    /**
     * @Route("/order/list", name="order.list", methods={"POST", "GET"})
     */
    public function getOrdersList($params): JsonResponse
    {
        $response = $this->reader->getOrdersList($params);
        return JsonResponse::fromJsonString($response);
    }

    /**
     * @Route("/order/add", name="order.add", methods={"POST"})
     */
    public function addOrder($params): JsonResponse
    {
        $response = $this->writer->addOrder($params);
        return JsonResponse::fromJsonString($response);
    }

    /**
     * @Route("/order/delete", name="order.delete", methods={"POST"})
     */
    public function deleteOrder($params): JsonResponse
    {
        $response = $this->writer->deleteOrder($params);
        return JsonResponse::fromJsonString($response);
    }

    /**
     * @Route("/client/list", name="client.list", methods={"POST", "GET"})
     */
    public function getClientsList($params): JsonResponse
    {
        $response = $this->reader->getClientsList($params);
        return JsonResponse::fromJsonString($response);
    }

    /**
     * @Route("/client/update", name="client.update", methods={"POST"})
     */
    public function updateClient($params): JsonResponse
    {
        $response = $this->writer->updateClient($params);
        return JsonResponse::fromJsonString($response);
    }
}