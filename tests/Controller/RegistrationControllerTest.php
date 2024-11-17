<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testRegistrationPageLoads(): void
    {
        $crawler = $this->client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Register');
    }

    public function testSuccessfulRegistration(): void
    {
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'newuser@example.com',
            'registration_form[firstName]' => 'New',
            'registration_form[lastName]' => 'User',
            'registration_form[plainPassword]' => 'password123',
            'registration_form[agreeTerms]' => true,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/login');

        // Verify user was created
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'newuser@example.com']);
        
        $this->assertNotNull($user);
        $this->assertEquals('New', $user->getFirstName());
        $this->assertEquals('User', $user->getLastName());
        $this->assertTrue(in_array('ROLE_USER', $user->getRoles()));
    }

    public function testRegistrationWithExistingEmail(): void
    {
        // First create a user
        $this->testSuccessfulRegistration();

        // Try to register with same email
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'newuser@example.com',
            'registration_form[firstName]' => 'Another',
            'registration_form[lastName]' => 'User',
            'registration_form[plainPassword]' => 'password123',
            'registration_form[agreeTerms]' => true,
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorExists('.invalid-feedback');
    }

    public function testRegistrationValidation(): void
    {
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'invalid-email',
            'registration_form[firstName]' => '',
            'registration_form[lastName]' => '',
            'registration_form[plainPassword]' => 'short',
            'registration_form[agreeTerms]' => false,
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(422);

        // Verify validation errors
        $this->assertSelectorExists('#registration_form_email.is-invalid');
        $this->assertSelectorExists('#registration_form_firstName.is-invalid');
        $this->assertSelectorExists('#registration_form_lastName.is-invalid');
        $this->assertSelectorExists('#registration_form_plainPassword.is-invalid');
        $this->assertSelectorExists('#registration_form_agreeTerms.is-invalid');
    }

    protected function tearDown(): void
    {
        // Remove test user
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'newuser@example.com']);
        
        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
