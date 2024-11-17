<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->passwordHasher = $this->client->getContainer()
            ->get('security.user_password_hasher');
    }

    public function testLoginPageLoads(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Sign In');
    }

    public function testLoginWithValidCredentials(): void
    {
        // Create a test user
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setFirstName('Test');
        $user->setLastName('User');
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Test login
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/');

        // Follow redirect
        $this->client->followRedirect();
        $this->assertSelectorExists('.user-menu');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLogout(): void
    {
        // First login
        $this->testLoginWithValidCredentials();

        // Then logout
        $this->client->request('GET', '/logout');
        $this->assertResponseRedirects();

        // Verify can't access protected page
        $this->client->request('GET', '/profile');
        $this->assertResponseRedirects('/login');
    }

    protected function tearDown(): void
    {
        // Remove test user
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'test@example.com']);
        
        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
