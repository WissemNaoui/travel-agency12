<?php

namespace App\Controller;

use App\Repository\PackageRepository;
use App\Repository\FlightRepository;
use App\Repository\HotelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        PackageRepository $packageRepository,
        FlightRepository $flightRepository,
        HotelRepository $hotelRepository
    ): Response {
        $destination = $request->query->get('destination');
        $checkIn = $request->query->get('check_in');
        $checkOut = $request->query->get('check_out');

        $searchResults = [];
        
        if ($destination || ($checkIn && $checkOut)) {
            // Calculate duration if dates are provided
            $duration = null;
            if ($checkIn && $checkOut) {
                $start = new \DateTime($checkIn);
                $end = new \DateTime($checkOut);
                $duration = $end->diff($start)->days;
            }

            // Search packages
            $searchResults['packages'] = $packageRepository->findBySearchCriteria([
                'destination' => $destination,
                'duration' => $duration,
                'status' => 'available'
            ]);

            // Search flights
            $searchResults['flights'] = $flightRepository->findBySearchCriteria([
                'destination' => $destination,
                'departure_date' => $checkIn ? new \DateTime($checkIn) : null
            ]);

            // Search hotels
            $searchResults['hotels'] = $hotelRepository->findBySearchCriteria([
                'city' => $destination,
                'guests' => $request->query->get('guests', 2) // Default to 2 guests
            ]);
        }

        // Get featured packages for homepage display
        $featuredPackages = $packageRepository->findBySearchCriteria(
            ['status' => 'available'],
            ['id' => 'DESC'],
            6
        );

        return $this->render('home/index.html.twig', [
            'featured_packages' => $featuredPackages,
            'search_results' => $searchResults,
            'search' => [
                'destination' => $destination,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
            ],
        ]);
    }
}
