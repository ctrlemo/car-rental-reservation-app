<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\VehicleSelectorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/manage', name: 'app_admin_index')]
    public function index(VehicleSelectorService $vehicleSelectorService): Response
    {
        return $this->render('admin/index.html.twig', [
            'vehicles' => $vehicleSelectorService->getAllVehicles(),
            'reservations' => $vehicleSelectorService->getAllReservations(),
        ]);
    }
}
