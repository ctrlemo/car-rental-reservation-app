<?php

declare(strict_types=1);

namespace App\Controller;

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


        // return new Response(
        //     '<html lang="en"><body>Lucky number: '.$number.'</body></html>'
        // );
    }
}
