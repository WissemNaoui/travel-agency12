<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\Package;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->count([]);
        $packages = $entityManager->getRepository(Package::class)->count([]);
        $bookings = $entityManager->getRepository(Booking::class)->count([]);

        $recentBookings = $entityManager->getRepository(Booking::class)
            ->findBy([], ['bookingDate' => 'DESC'], 5);

        return $this->render('admin/dashboard.html.twig', [
            'stats' => [
                'users' => $users,
                'packages' => $packages,
                'bookings' => $bookings,
            ],
            'recent_bookings' => $recentBookings,
        ]);
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/packages', name: 'app_admin_packages')]
    public function packages(EntityManagerInterface $entityManager): Response
    {
        $packages = $entityManager->getRepository(Package::class)->findAll();

        return $this->render('admin/packages.html.twig', [
            'packages' => $packages,
        ]);
    }

    #[Route('/bookings', name: 'app_admin_bookings')]
    public function bookings(EntityManagerInterface $entityManager): Response
    {
        $bookings = $entityManager->getRepository(Booking::class)->findAll();

        return $this->render('admin/bookings.html.twig', [
            'bookings' => $bookings,
        ]);
    }
}
