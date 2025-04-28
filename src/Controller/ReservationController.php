<?php

declare(strict_types=1);

namespace App\Controller;

//external
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use DateTime;

//local
use App\Service\VehicleSelectorService;
use App\Constants\AppConstants;

class ReservationController extends AbstractController
{
    #[Route('/results', name: AppConstants::ROUTE_RESERVATION_INDEX)]
    public function index(SessionInterface $session, VehicleSelectorService $vehicleSelectorService): Response
    {
    // Retrieve reservation data from the session
    $reservationData = $session->get(AppConstants::SESSION_RESERVATION_KEY);
    // dd($reservationData);
    if (!$reservationData) {
        $this->addFlash('notice', 'No reservation data found.');
        return $this->redirectToRoute(AppConstants::ROUTE_HOME);
    }

    // Use VehicleSelectorService to find available vehicles
    // $availableVehicles = $vehicleSelectorService->findAvailableVehicles(
    //     new DateTime($reservationData['startDate']),
    //     new DateTime($reservationData['endDate']),
    //     $reservationData['passengerCount'],
    //     $reservationData['userEmail']
    // );

    // if (empty($availableVehicles)) {
    //     $this->addFlash('notice', 'No vehicles are available for the selected dates.');
    //     return $this->redirectToRoute(AppConstants::ROUTE_HOME);
    // }

    // return $this->render('reservation/index.html.twig', [
    //     'availableVehicles' => $availableVehicles,
    // ]);

    return $this->render('reservation/index.html.twig');
    }
}
