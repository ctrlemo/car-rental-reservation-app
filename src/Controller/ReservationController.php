<?php

declare(strict_types=1);

namespace App\Controller;

//external
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

//local
use App\Service\VehicleSelectorService;
use App\Constants\AppConstants;
use App\Entity\Reservation;
use App\Form\VehicleSelectionType;

class ReservationController extends AbstractController
{
    #[Route('/results', name: AppConstants::ROUTE_RESERVATION_INDEX)]
    public function index(SessionInterface $session, VehicleSelectorService $vehicleSelectorService, Request $request): Response
    {
        // Retrieve reservation data from the session
        $reservation = $session->get(AppConstants::SESSION_RESERVATION_KEY);
        // dd($reservation);
        if (!$reservation || !$reservation instanceof Reservation) {
            $this->addFlash('error', 'No reservation data found.');
            return $this->redirectToRoute(AppConstants::ROUTE_HOME);
        }

        // Use VehicleSelectorService to find available vehicles
        $availableVehicles = $vehicleSelectorService->findAvailableVehicles(
            $reservation->getStartDate(),
            $reservation->getEndDate(),
            $reservation->getPassengerCount(),
            $reservation->getUserEmail()
        );

        //dd($availableVehicles);

        if (empty($availableVehicles)) {
            $this->addFlash('notice', 'No vehicles are available for the selected dates.');
            return $this->redirectToRoute(AppConstants::ROUTE_HOME);
        }
        
        // Check for errors in the available vehicles array
        $flashType = array_intersect_key(array_flip(['error', 'warning', 'notice']), $availableVehicles);
        if ($flashType) {
            $this->addFlash(array_key_first($flashType), 'An error occurred while fetching available vehicles. ' . $availableVehicles['message']);
            return $this->redirectToRoute(AppConstants::ROUTE_HOME);
        }

        // Create the vehicle selection form
        $form = $this->createForm(VehicleSelectionType::class, null, [
            'vehicles' => $availableVehicles, // Pass available vehicles to the form
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Combine the reservation data with the selected vehicle
            $reservation->setVehicle($data['vehicle']);
            dd($reservation);
            // Save the finalized reservation using the VehicleSelectorService
            // $vehicleSelectorService->finalizeReservation($reservation);

            $this->addFlash('success', 'Reservation confirmed!');
            return $this->redirectToRoute('app_reservation_confirm');
        }

        return $this->render('reservation/index.html.twig', [
            'form' => $form,
            'reservation' => $reservation,
            'availableVehicles' => $availableVehicles,
        ]);
    }
}
