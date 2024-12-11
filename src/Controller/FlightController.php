<?php

namespace App\Controller;

use App\Repository\FlightRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;
#[Route('/flights')]
class FlightController extends AbstractController
{/*
    #[Route('/', name: 'app_flights')]
    public function index(Request $request, FlightRepository $flightRepository): Response
    {
        $origin = $request->query->get('origin');
        $destination = $request->query->get('destination');
        $price = $request->query->get('price');
        $departureTime = $request->query->get('departureTime');
        $arrivalTime = $request->query->get('arrivalTime');
        $sort = $request->query->get('sort');
    
        // Build the query builder
        $queryBuilder = $flightRepository->createQueryBuilder('f');
    
        // Apply filters
        if ($origin) {
            $queryBuilder->andWhere('f.origin LIKE :origin')
                         ->setParameter('origin', '%' . $origin . '%');
        }
    
        if ($destination) {
            $queryBuilder->andWhere('f.destination LIKE :destination')
                         ->setParameter('destination', '%' . $destination . '%');
        }
    
        if ($price) {
            list($minPrice, $maxPrice) = explode('-', $price);
            if ($maxPrice) {
                $queryBuilder->andWhere('f.price BETWEEN :minPrice AND :maxPrice')
                             ->setParameter('minPrice', (float)$minPrice)
                             ->setParameter('maxPrice', (float)$maxPrice);
            } else {
                $queryBuilder->andWhere('f.price >= :minPrice')
                             ->setParameter('minPrice', (float)$minPrice);
            }
        }
    
        if ($departureTime) {
            $queryBuilder->andWhere('f.departureTime = :departureTime')
                         ->setParameter('departureTime', new \DateTime($departureTime));
        }
    
        if ($arrivalTime) {
            $queryBuilder->andWhere('f.arrivalTime = :arrivalTime')
                         ->setParameter('arrivalTime', new \DateTime($arrivalTime));
        }
    
        // Apply sorting
        switch ($sort) {
            case 'price_asc':
                $queryBuilder->orderBy('f.price', 'ASC');
                break;
            case 'price_desc':
                $queryBuilder->orderBy('f.price', 'DESC');
                break;
            case 'date_asc':
                $queryBuilder->orderBy('f.departureTime', 'ASC');
                break;
            case 'date_desc':
                $queryBuilder->orderBy('f.departureTime', 'DESC');
                break;
            default:
                $queryBuilder->orderBy('f.origin', 'ASC');
                break;
        }
    
        // Execute the query
        $flights = $queryBuilder->getQuery()->getResult();
    
        return $this->render('flight/index.html.twig', [
            'flights' => $flights,
        ]);
    }
    */
    #[Route('/', name: 'app_flights')]

    public function index(Request $request, FlightRepository $flightRepository): Response
    {
        $origin = $request->query->get('origin');
        $destination = $request->query->get('destination');
        $price = $request->query->get('price');
        $departureTime = $request->query->get('departureTime');
        $arrivalTime = $request->query->get('arrivalTime');
        $sort = $request->query->get('sort');
        $currentPage = max(1, $request->query->getInt('page', 1));
        $itemsPerPage = 12;
    
        // Build the query builder
        $queryBuilder = $flightRepository->createQueryBuilder('f');
    
        if ($origin) {
            $queryBuilder->andWhere('f.origin LIKE :origin')
                         ->setParameter('origin', '%' . $origin . '%');
        }
    
        if ($destination) {
            $queryBuilder->andWhere('f.destination LIKE :destination')
                         ->setParameter('destination', '%' . $destination . '%');
        }
    
        if ($price) {
            list($minPrice, $maxPrice) = explode('-', $price);
            if ($maxPrice) {
                $queryBuilder->andWhere('f.price BETWEEN :minPrice AND :maxPrice')
                             ->setParameter('minPrice', (float)$minPrice)
                             ->setParameter('maxPrice', (float)$maxPrice);
            } else {
                $queryBuilder->andWhere('f.price >= :minPrice')
                             ->setParameter('minPrice', (float)$minPrice);
            }
        }
    
        if ($departureTime) {
            $queryBuilder->andWhere('f.departureTime = :departureTime')
                         ->setParameter('departureTime', new \DateTime($departureTime));
        }
    
        if ($arrivalTime) {
            $queryBuilder->andWhere('f.arrivalTime = :arrivalTime')
                         ->setParameter('arrivalTime', new \DateTime($arrivalTime));
        }
    
        if ($sort === 'price_asc') {
            $queryBuilder->orderBy('f.price', 'ASC');
        } elseif ($sort === 'price_desc') {
            $queryBuilder->orderBy('f.price', 'DESC');
        } elseif ($sort === 'date_asc') {
            $queryBuilder->orderBy('f.departureTime', 'ASC');
        } elseif ($sort === 'date_desc') {
            $queryBuilder->orderBy('f.departureTime', 'DESC');
        } else {
            $queryBuilder->orderBy('f.origin', 'ASC');
        }
    
        // Pagination
        $totalItems = count($queryBuilder->getQuery()->getResult());
        $totalPages = ceil($totalItems / $itemsPerPage);
        $flights = $queryBuilder->setFirstResult(($currentPage - 1) * $itemsPerPage)
                                ->setMaxResults($itemsPerPage)
                                ->getQuery()
                                ->getResult();
    
        return $this->render('flight/index.html.twig', [
            'flights' => $flights,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }
    

    #[Route('/search', name: 'app_flight_search', methods: ['GET'])]
    public function search(Request $request, FlightRepository $flightRepository, PaginatorInterface $paginator): Response
    {
        // Get paginated flights based on search criteria
        $pagination = $this->getPaginatedFlights($flightRepository, $paginator, $request);

        // Render the response (supports AJAX if requested)
        if ($request->isXmlHttpRequest()) {
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

    private function getPaginatedFlights(FlightRepository $flightRepository, PaginatorInterface $paginator, Request $request): \Knp\Component\Pager\Pagination\PaginationInterface
    {
        // Build the query with search criteria
        $queryBuilder = $flightRepository->createQueryBuilder('f');

        // Apply search filters to the query
        $this->applySearchCriteria($queryBuilder, $request);

        // Order flights by departure time (earliest first)
        $queryBuilder->orderBy('f.departureTime', 'ASC');

        // Return paginated results
        return $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Adjust the items per page as needed
        );
    }

    private function applySearchCriteria($queryBuilder, Request $request): void
{
    $origin = $request->query->get('origin');
    $destination = $request->query->get('destination');
    $departureDate = $request->query->get('departureDate');
    $airline = $request->query->get('airline');
    $minSeats = $request->query->getInt('minSeats', 0);
    $minPrice = $request->query->getInt('minPrice', 0);
    $maxPrice = $request->query->getInt('maxPrice', PHP_INT_MAX);

    if ($origin) {
        $queryBuilder
            ->andWhere('LOWER(f.origin) LIKE LOWER(:origin)')
            ->setParameter('origin', '%' . strtolower(trim($origin)) . '%');
    }

    if ($destination) {
        $queryBuilder
            ->andWhere('LOWER(f.destination) LIKE LOWER(:destination)')
            ->setParameter('destination', '%' . strtolower(trim($destination)) . '%');
    }

    if ($departureDate && \DateTime::createFromFormat('Y-m-d', $departureDate)) {
        $startOfDay = \DateTime::createFromFormat('Y-m-d', $departureDate)->setTime(0, 0, 0);
        $endOfDay = \DateTime::createFromFormat('Y-m-d', $departureDate)->setTime(23, 59, 59);

        $queryBuilder
            ->andWhere('f.departureTime BETWEEN :start AND :end')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay);
    }

    if ($airline) {
        $queryBuilder
            ->andWhere('LOWER(f.airline) LIKE LOWER(:airline)')
            ->setParameter('airline', '%' . strtolower(trim($airline)) . '%');
    }

    if ($minSeats > 0) {
        $queryBuilder
            ->andWhere('f.availableSeats >= :minSeats')
            ->setParameter('minSeats', $minSeats);
    }

    if ($minPrice > 0) {
        $queryBuilder
            ->andWhere('f.price >= :minPrice')
            ->setParameter('minPrice', $minPrice);
    }

    if ($maxPrice !== PHP_INT_MAX) {
        $queryBuilder
            ->andWhere('f.price <= :maxPrice')
            ->setParameter('maxPrice', $maxPrice);
    }
}
}
