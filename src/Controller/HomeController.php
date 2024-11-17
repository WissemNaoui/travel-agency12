<?php

namespace App\Controller;

use App\Repository\PackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PackageRepository $packageRepository): Response
    {
        // Get featured packages (we'll assume the first 6 available packages are featured)
        $featuredPackages = $packageRepository->findBy(
            ['status' => 'available'],
            ['id' => 'DESC'],
            6
        );

        return $this->render('home/index.html.twig', [
            'featured_packages' => $featuredPackages,
        ]);
    }
}
