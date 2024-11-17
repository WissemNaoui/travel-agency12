<?php

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use App\Entity\TravelPackage;
use App\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $adminUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        // Create and login as admin user
        $this->createAndLoginAdmin();
    }

    private function createAndLoginAdmin(): void
    {
        $passwordHasher = $this->client->getContainer()->get('security.user_password_hasher');
        
        $this->adminUser = new User();
        $this->adminUser->setEmail('admin@example.com');
        $this->adminUser->setFirstName('Admin');
        $this->adminUser->setLastName('User');
        $this->adminUser->setPassword($passwordHasher->hashPassword($this->adminUser, 'admin123'));
        $this->adminUser->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($this->adminUser);
        $this->entityManager->flush();

        // Login
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'admin@example.com',
            'password' => 'admin123',
        ]);
    }

    public function testAdminDashboard(): void
    {
        $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Dashboard');
        
        // Check for statistics cards
        $this->assertSelectorExists('.stats-card.users');
        $this->assertSelectorExists('.stats-card.packages');
        $this->assertSelectorExists('.stats-card.bookings');
    }

    public function testUserManagement(): void
    {
        // Create a regular user for testing
        $regularUser = new User();
        $regularUser->setEmail('user@example.com');
        $regularUser->setFirstName('Regular');
        $regularUser->setLastName('User');
        $regularUser->setPassword('password123');
        $regularUser->setRoles(['ROLE_USER']);

        $this->entityManager->persist($regularUser);
        $this->entityManager->flush();

        // Test users list
        $this->client->request('GET', '/admin/users');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', 'user@example.com');

        // Test user edit
        $this->client->request('GET', '/admin/users/' . $regularUser->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        // Test user delete
        $this->client->request('DELETE', '/admin/users/' . $regularUser->getId());
        $this->assertResponseRedirects('/admin/users');

        $this->entityManager->remove($regularUser);
        $this->entityManager->flush();
    }

    public function testPackageManagement(): void
    {
        // Create a test package
        $package = new TravelPackage();
        $package->setName('Test Package');
        $package->setDescription('Test Description');
        $package->setPrice(1000);
        $package->setDuration(7);
        $package->setDestination('Test Destination');

        $this->entityManager->persist($package);
        $this->entityManager->flush();

        // Test packages list
        $this->client->request('GET', '/admin/packages');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', 'Test Package');

        // Test package edit
        $this->client->request('GET', '/admin/packages/' . $package->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        // Test package delete
        $this->client->request('DELETE', '/admin/packages/' . $package->getId());
        $this->assertResponseRedirects('/admin/packages');

        $this->entityManager->remove($package);
        $this->entityManager->flush();
    }

    public function testBookingManagement(): void
    {
        // Create a test package
        $package = new TravelPackage();
        $package->setName('Test Package');
        $package->setDescription('Test Description');
        $package->setPrice(1000);
        $package->setDuration(7);
        $package->setDestination('Test Destination');

        $this->entityManager->persist($package);

        // Create a test booking
        $booking = new Booking();
        $booking->setUser($this->adminUser);
        $booking->setPackage($package);
        $booking->setBookingDate(new \DateTime());
        $booking->setTravelDate(new \DateTime('+1 month'));
        $booking->setStatus('pending');

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        // Test bookings list
        $this->client->request('GET', '/admin/bookings');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', 'Test Package');

        // Test booking view
        $this->client->request('GET', '/admin/bookings/' . $booking->getId());
        $this->assertResponseIsSuccessful();

        // Test booking status update
        $this->client->request('POST', '/admin/bookings/' . $booking->getId() . '/status', [
            'status' => 'confirmed'
        ]);
        $this->assertResponseRedirects('/admin/bookings');

        // Clean up
        $this->entityManager->remove($booking);
        $this->entityManager->remove($package);
        $this->entityManager->flush();
    }

    public function testAccessControl(): void
    {
        // Logout admin
        $this->client->request('GET', '/logout');

        // Create and login as regular user
        $regularUser = new User();
        $regularUser->setEmail('regular@example.com');
        $regularUser->setFirstName('Regular');
        $regularUser->setLastName('User');
        $regularUser->setPassword($this->client->getContainer()
            ->get('security.user_password_hasher')
            ->hashPassword($regularUser, 'password123'));
        $regularUser->setRoles(['ROLE_USER']);

        $this->entityManager->persist($regularUser);
        $this->entityManager->flush();

        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            'email' => 'regular@example.com',
            'password' => 'password123',
        ]);

        // Try accessing admin pages
        $adminPages = [
            '/admin',
            '/admin/users',
            '/admin/packages',
            '/admin/bookings',
        ];

        foreach ($adminPages as $page) {
            $this->client->request('GET', $page);
            $this->assertResponseStatusCodeSame(403);
        }

        // Clean up
        $this->entityManager->remove($regularUser);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        if ($this->adminUser) {
            $this->entityManager->remove($this->adminUser);
            $this->entityManager->flush();
        }

        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
