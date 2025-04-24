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
             dd($reservation);
            //return $this->redirectToRoute('app_reservation_index');
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
