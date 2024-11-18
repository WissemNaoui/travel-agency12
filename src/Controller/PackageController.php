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
    #[Route('/test', name: 'app_packagesa')]
    public function indexs(PackageRepository $packageRepository): Response
    {
        $packages = $packageRepository->findBy(['status' => 'available']);

        return $this->render('package/index.html.twig', [
            'packages' => $packages,
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

    #[Route('/', name: 'app_packages')]
    public function index(Request $request, PackageRepository $packageRepository)
    {
        $destination = $request->query->get('destination');
        $duration = $request->query->get('duration');
        $price = $request->query->get('price');
        $sort = $request->query->get('sort');

        // Build the query builder
        $queryBuilder = $packageRepository->createQueryBuilder('p');

        // Apply filters
        if ($destination) {
            $queryBuilder->andWhere('p.destination LIKE :destination')
                         ->setParameter('destination', '%' . $destination . '%');
        }

        if ($duration) {
            // First, ensure we are dealing with a valid format (e.g., "1-3" or "15+")
            if (strpos($duration, '-') !== false) {
                list($minDuration, $maxDuration) = explode('-', $duration);
                
                // Ensure the values are numeric and fall back to default values if not
                $minDuration = (int)$minDuration;
                if ($maxDuration) {
                    $maxDuration = (int)$maxDuration;
                    $queryBuilder->andWhere('p.duration BETWEEN :minDuration AND :maxDuration')
                                 ->setParameter('minDuration', $minDuration)
                                 ->setParameter('maxDuration', $maxDuration);
                } else {
                    $queryBuilder->andWhere('p.duration >= :minDuration')
                                 ->setParameter('minDuration', $minDuration);
                }
            } elseif ($duration === '15+') {
                // Special case for durations greater than 15 days
                $queryBuilder->andWhere('p.duration >= :minDuration')
                             ->setParameter('minDuration', 15);
            }
        }

        if ($price) {
            list($minPrice, $maxPrice) = explode('-', $price);
            if ($maxPrice) {
                $queryBuilder->andWhere('p.price BETWEEN :minPrice AND :maxPrice')
                             ->setParameter('minPrice', (float)$minPrice)
                             ->setParameter('maxPrice', (float)$maxPrice);
            } else {
                $queryBuilder->andWhere('p.price >= :minPrice')
                             ->setParameter('minPrice', (float)$minPrice);
            }
        }

        // Apply sorting
        switch ($sort) {
            case 'price_asc':
                $queryBuilder->orderBy('p.price', 'ASC');
                break;
            case 'price_desc':
                $queryBuilder->orderBy('p.price', 'DESC');
                break;
            case 'duration_asc':
                $queryBuilder->orderBy('p.duration', 'ASC');
                break;
            case 'duration_desc':
                $queryBuilder->orderBy('p.duration', 'DESC');
                break;
            default:
                $queryBuilder->orderBy('p.name', 'ASC');
                break;
        }

        // Execute the query
        $packages = $queryBuilder->getQuery()->getResult();

        return $this->render('package/index.html.twig', [
            'packages' => $packages,
        ]);
    }

}





        