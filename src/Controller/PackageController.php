<?php

namespace App\Controller;

use App\Repository\PackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/packages')]
class PackageController extends AbstractController
{
    #[Route('', name: 'app_packages')]
    public function index(PackageRepository $packageRepository): Response
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
}
