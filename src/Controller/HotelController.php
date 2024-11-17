<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hotels')]
class HotelController extends AbstractController
{
    #[Route('', name: 'app_hotels')]
    public function index(): Response
    {
        return $this->render('hotel/index.html.twig', [
            'hotels' => [], // TODO: Add hotel repository and fetch hotels
        ]);
    }

    #[Route('/{id}', name: 'app_hotel_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        // TODO: Add hotel repository and fetch hotel by id
        return $this->render('hotel/show.html.twig', [
            'hotel' => null,
        ]);
    }
}
