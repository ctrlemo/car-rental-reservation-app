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
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $today = new DateTime();
        $reservation = new Reservation();
        $reservation->setStartDate($today);
        $reservation->setEndDate((clone $today)->modify("+1 day"));
        $reservation->setPassengerCount(1);

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
            $session->set('reservation', [
                'startDate' => $reservation->getStartDate(),
                'endDate' => $reservation->getEndDate(),
                'passengerCount' => $reservation->getPassengerCount(),
                'userEmail' => $reservation->getUserEmail(),
            ]);

            // Redirect to a confirmation page or another action.
            return $this->redirectToRoute('app_reservation_index');
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
