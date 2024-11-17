<?php

namespace App\Controller;

use App\Repository\FlightRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/flights')]
class FlightController extends AbstractController
{
    #[Route('/', name: 'app_flight_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('flight/index.html.twig');
    }

    #[Route('/search', name: 'app_flight_search', methods: ['GET'])]
    public function search(Request $request, FlightRepository $flightRepository): Response
    {
        $criteria = [
            'origin' => $request->query->get('origin'),
            'destination' => $request->query->get('destination'),
            'departureDate' => $request->query->get('departureDate'),
            'minPrice' => $request->query->get('minPrice'),
            'maxPrice' => $request->query->get('maxPrice'),
            'airline' => $request->query->get('airline'),
            'minSeats' => $request->query->get('minSeats'),
        ];

        $flights = $flightRepository->findBySearchCriteria($criteria);

        if ($request->headers->get('X-Requested-With') === 'XMLHttpRequest') {
            return $this->render('flight/_flight_list.html.twig', [
                'flights' => $flights,
            ]);
        }

        return $this->render('flight/index.html.twig', [
            'flights' => $flights,
        ]);
    }

    #[Route('/{id}', name: 'app_flight_show', methods: ['GET'])]
    public function show(int $id, FlightRepository $flightRepository): Response
    {
        $flight = $flightRepository->find($id);

        if (!$flight) {
            throw $this->createNotFoundException('Flight not found');
        }

        return $this->render('flight/show.html.twig', [
            'flight' => $flight,
        ]);
    }

    #[Route('/ajax-example', name: 'app_flight_ajax_example', methods: ['GET'])]
    public function ajaxExample(): Response
    {
        return $this->render('flight/ajax-example.html.twig');
    }
}
