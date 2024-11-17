<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Package;
use App\Entity\Flight;
use App\Entity\Booking;
use App\Entity\Hotel;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Option 1. You can make your dashboard redirect to some common page of your backend
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(FlightCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Travel Agency Admin')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Travel Management');
        yield MenuItem::linkToCrud('Packages', 'fa fa-suitcase', Package::class);
        yield MenuItem::linkToCrud('Flights', 'fa fa-plane', Flight::class);
        yield MenuItem::linkToCrud('Hotels', 'fa fa-hotel', Hotel::class);
        yield MenuItem::linkToCrud('Bookings', 'fa fa-calendar-check', Booking::class);

        yield MenuItem::section('User Management');
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);

        yield MenuItem::section('Website');
        yield MenuItem::linkToRoute('Homepage', 'fa fa-globe', 'app_home');
    }
}
