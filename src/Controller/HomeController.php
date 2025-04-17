<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $number = random_int(0, 100);

        return $this->render('home/index.html.twig', [
            'number' => $number,
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
