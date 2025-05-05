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

use Psr\Log\LoggerInterface;

class ReservationController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
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
            //dd($reservation);
            // Save the finalized reservation using the VehicleSelectorService
            $vehicleSelectorService->finalizeReservation($reservation);

            // Store the reservation in the session
            $session->set(AppConstants::SESSION_RESERVATION_KEY, $reservation);
            $this->addFlash('notice', 'Reservation confirmed!');
            return $this->redirectToRoute(AppConstants::ROUTE_RESERVATION_CONFIRM);
        }

        return $this->render('reservation/index.html.twig', [
            'form' => $form,
            'reservation' => $reservation,
            'availableVehicles' => $availableVehicles,
        ]);
    }

    #[Route('/confirm', name: AppConstants::ROUTE_RESERVATION_CONFIRM)]
    /**
     * @param SessionInterface $session
     * @return Response
     */
    // Confirm the reservation and display the confirmation page
    // This method is responsible for displaying the reservation confirmation page.
    // It retrieves the reservation data from the session and renders the confirmation template.
    // If the reservation data is not found in the session, it redirects to the home page with an error message.
    public function confirm(SessionInterface $session): Response
    {
        // Retrieve reservation data from the session
        $reservation = $session->get(AppConstants::SESSION_RESERVATION_KEY);
        if (!$reservation || !$reservation instanceof Reservation) {
            $this->addFlash('error', 'No reservation data found.');
            return $this->redirectToRoute(AppConstants::ROUTE_HOME);
        }

        // Check if the reservation is finalized
        if (!$reservation->getVehicle() || !$reservation->getId()) {
            // log the error
            $this->logger->error(AppConstants::RESERVATION_NOT_FINALIZED, [
                'reservation' => $reservation,
            ]);
            $this->addFlash('error', AppConstants::RESERVATION_NOT_FINALIZED);
            return $this->redirectToRoute(AppConstants::ROUTE_HOME);
        }

        // clear the session, so the reservation is not available anymore
        // This is important to prevent the user from accessing the reservation data again
        $session->clear();
        return $this->render('reservation/confirm.html.twig', [
            'reservation' => $reservation,
        ]);
    }
}
