<?php

namespace App\Controller;

use App\Repository\PackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/packages')]
class PackageController extends AbstractController
{
    #[Route('/', name: 'app_packages')]
    public function index(Request $request, PackageRepository $packageRepository)
    {
        $destination = $request->query->get('destination');
        $duration = $request->query->get('duration');
        $price = $request->query->get('price');
        $sort = $request->query->get('sort');

        // Build the query builder
        $queryBuilder = $packageRepository->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', 'available');

        // Apply filters
        if ($destination) {
            $queryBuilder->andWhere('p.destination LIKE :destination')
                ->setParameter('destination', '%' . $destination . '%');
        }

        if ($duration) {
            $queryBuilder->andWhere('p.duration <= :duration')
                ->setParameter('duration', $duration);
        }

        if ($price) {
            $queryBuilder->andWhere('p.price <= :price')
                ->setParameter('price', $price);
        }

        // Apply sorting
        if ($sort === 'price_low') {
            $queryBuilder->orderBy('p.price', 'ASC');
        } elseif ($sort === 'price_high') {
            $queryBuilder->orderBy('p.price', 'DESC');
        } elseif ($sort === 'duration_low') {
            $queryBuilder->orderBy('p.duration', 'ASC');
        } elseif ($sort === 'duration_high') {
            $queryBuilder->orderBy('p.duration', 'DESC');
        } else {
            $queryBuilder->orderBy('p.id', 'DESC');
        }

        $packages = $queryBuilder->getQuery()->getResult();

        return $this->render('package/index.html.twig', [
            'packages' => $packages,
            'filters' => [
                'destination' => $destination,
                'duration' => $duration,
                'price' => $price,
                'sort' => $sort
            ]
        ]);
    }

    #[Route('/{id}', name: 'app_package_show', requirements: ['id' => '\d+'])]
    public function show(int $id, PackageRepository $packageRepository): Response
    {
        $package = $packageRepository->find($id);

        if (!$package) {
            throw $this->createNotFoundException('Package not found');
        }

        return $this->render('package/show.html.twig', [
            'package' => $package,
        ]);
    }

    #[Route('/test', name: 'app_packagesa')]
    public function indexs(PackageRepository $packageRepository): Response
    {
        $packages = $packageRepository->findBy(['status' => 'available']);

        return $this->render('package/index.html.twig', [
            'packages' => $packages,
        ]);
    }
}