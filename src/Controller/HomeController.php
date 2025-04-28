<?php

declare(strict_types=1);

namespace App\Controller;

//external
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use DateTime;

//local
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Constants\AppConstants;
class HomeController extends AbstractController
{
    #[Route('/', name: AppConstants::ROUTE_HOME)]
    /**
     * This method handles the home page and displays the reservation form.
     * It also handles the form submission and stores the reservation data in the session.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $today = new DateTime();
        $reservation = new Reservation();
        $reservation->setStartDate($today);
        $reservation->setEndDate($today->modify('+' . AppConstants::DEFAULT_RESERVATION_DAYS . ' days'));
        $reservation->setPassengerCount(AppConstants::DEFAULT_PASSENGER_COUNT);

        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $reservation = $form->getData();
             // dd($reservation);

            // Store the reservation data in the session.
            // Note: I could just pass the reservation data through querystring parameters to the next page
            // since it is not sensitive data like password or credit card number.
            // but I don't want to expose the reservation data in the URL and have to worry about validating it again on the next page.
            $session = $request->getSession();
            $session->set(AppConstants::SESSION_RESERVATION_KEY, [
                'startDate' => $reservation->getStartDate(),
                'endDate' => $reservation->getEndDate(),
                'passengerCount' => $reservation->getPassengerCount(),
                'userEmail' => $reservation->getUserEmail(),
            ]);

            // Redirect to a confirmation page or another action.
            return $this->redirectToRoute(AppConstants::ROUTE_RESERVATION_INDEX);
        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/log', name: 'app_log')]
    /**
     * This method demonstrates how to log a message using the logger service.
     */
    public function  logSomething(LoggerInterface $logger): Response
    {
        $logger->info('This is a log message');
        return new Response('Log message recorded.');
    }
}
