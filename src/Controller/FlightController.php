<?php

namespace App\Controller;

use App\Repository\FlightRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/flights')]
class FlightController extends AbstractController
{
    #[Route('/', name: 'app_flight_index', methods: ['GET'])]
    public function index(FlightRepository $flightRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $flightRepository->createQueryBuilder('f')
            ->where('f.status = :status')
            ->setParameter('status', 'scheduled')
            ->orderBy('f.departureTime', 'ASC');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('flight/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/search', name: 'app_flight_search', methods: ['GET'])]
    public function search(Request $request, FlightRepository $flightRepository, PaginatorInterface $paginator): Response
    {
        $criteria = [
            'origin' => $request->query->get('origin'),
            'destination' => $request->query->get('destination'),
            'departureTime' => $request->query->get('departureDate'),
            'minPrice' => $request->query->get('minPrice'),
            'maxPrice' => $request->query->get('maxPrice'),
            'airline' => $request->query->get('airline'),
            'minSeats' => $request->query->get('minSeats'),
        ];

        $queryBuilder = $flightRepository->createQueryBuilder('f')
            ->where('f.origin LIKE :origin')
            ->setParameter('origin', '%' . $criteria['origin'] . '%')
            ->andWhere('f.destination LIKE :destination')
            ->setParameter('destination', '%' . $criteria['destination'] . '%');

        if ($criteria['departureTime']) {
            $queryBuilder
                ->andWhere('DATE_FORMAT(f.departureTime, \'%Y-%m-%d\') = :departureTime')
                ->setParameter('departureTime', $criteria['departureTime']);
        }

        $queryBuilder
            ->andWhere('f.price >= :minPrice')
            ->setParameter('minPrice', $criteria['minPrice'] ?: 0)
            ->andWhere('f.price <= :maxPrice')
            ->setParameter('maxPrice', $criteria['maxPrice'] ?: PHP_FLOAT_MAX)
            ->andWhere('f.airline LIKE :airline')
            ->setParameter('airline', '%' . $criteria['airline'] . '%')
            ->andWhere('f.availableSeats >= :minSeats')
            ->setParameter('minSeats', $criteria['minSeats'] ?: 0)
            ->orderBy('f.departureTime', 'ASC');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            12
        );

        if ($request->headers->get('X-Requested-With') === 'XMLHttpRequest') {
            return $this->render('flight/_flight_list.html.twig', [
                'pagination' => $pagination,
            ]);
        }

        return $this->render('flight/index.html.twig', [
            'pagination' => $pagination,
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
