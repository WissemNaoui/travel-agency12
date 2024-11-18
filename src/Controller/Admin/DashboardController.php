<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Package;
use App\Entity\Flight;
use App\Entity\Booking;
use App\Entity\Hotel;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $stats = [
            'users' => $this->entityManager->getRepository(User::class)->count([]),
            'packages' => $this->entityManager->getRepository(Package::class)->count([]),
            'bookings' => $this->entityManager->getRepository(Booking::class)->count([]),
            'flights' => $this->entityManager->getRepository(Flight::class)->count([]),
            'hotels' => $this->entityManager->getRepository(Hotel::class)->count([]),
        ];

        // Get recent bookings
        $recentBookings = $this->entityManager->getRepository(Booking::class)
            ->findBy([], ['bookingDate' => 'DESC'], 5);

        return $this->render('admin/index.html.twig', [
            'stats' => $stats,
            'recent_bookings' => $recentBookings
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Travel Agency Admin')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Travel Management');
        yield MenuItem::linkToCrud('Flights', 'fa fa-plane', Flight::class)
            ->setController(FlightCrudController::class);
        yield MenuItem::linkToCrud('Hotels', 'fa fa-hotel', Hotel::class)
            ->setController(HotelCrudController::class);
        yield MenuItem::linkToCrud('Packages', 'fa fa-suitcase', Package::class)
            ->setController(PackageCrudController::class);
        yield MenuItem::linkToCrud('Bookings', 'fa fa-calendar-check', Booking::class)
            ->setController(BookingCrudController::class);

        yield MenuItem::section('User Management');
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class)
            ->setController(UserCrudController::class);

        yield MenuItem::section('Settings');
        yield MenuItem::linkToUrl('Back to Website', 'fa fa-arrow-left', '/');
    }
}
