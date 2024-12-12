<?php

namespace App\Controller;

use App\Repository\PackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, PackageRepository $packageRepository): Response
    {
        $destination = $request->query->get('destination');
        $checkIn = $request->query->get('check_in');
        $checkOut = $request->query->get('check_out');

        // Build search criteria
        $criteria = ['status' => 'available'];
        if ($destination) {
            $criteria['destination'] = $destination;
        }
        if ($checkIn && $checkOut) {
            $criteria['start_date'] = new \DateTime($checkIn);
            $criteria['end_date'] = new \DateTime($checkOut);
        }

        // Get packages using the new search method
        $featuredPackages = $packageRepository->findBySearchCriteria(
            $criteria,
            ['id' => 'DESC'],
            6
        );

        return $this->render('home/index.html.twig', [
            'featured_packages' => $featuredPackages,
            'search' => [
                'destination' => $destination,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
            ],
        ]);
    }
}
