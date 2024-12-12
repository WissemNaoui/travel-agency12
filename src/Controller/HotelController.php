<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Repository\HotelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/hotels')]
class HotelController extends AbstractController
{
    private $hotelRepository;

    public function __construct(HotelRepository $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }

    #[Route('', name: 'app_hotels')]
    public function index(): Response
    {
        $hotels = $this->hotelRepository->findAll();
        
        return $this->render('hotel/index.html.twig', [
            'hotels' => $hotels,
        ]);
    }

    #[Route('/{id}', name: 'app_hotel_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $hotel = $this->hotelRepository->find($id);
        
        if (!$hotel) {
            throw $this->createNotFoundException('Hotel not found');
        }

        return $this->render('hotel/show.html.twig', [
            'hotel' => $hotel,
        ]);
    }
}
