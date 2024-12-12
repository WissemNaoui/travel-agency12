<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Flight;
use App\Entity\Hotel;
use App\Entity\Package;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private HotelRepository $hotelRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        HotelRepository $hotelRepository
    ) {
        $this->entityManager = $entityManager;
        $this->hotelRepository = $hotelRepository;
    }

    #[Route('/bookings', name: 'app_bookings')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $bookings = $this->entityManager->getRepository(Booking::class)->findBy(
            ['user' => $user],
            ['bookingDate' => 'DESC']
        );

        return $this->render('booking/index.html.twig', [
            'bookings' => $bookings
        ]);
    }

    #[Route('/bookings/{id}', name: 'app_booking_show')]
    public function show(Booking $booking): Response
    {
        // Check if the current user owns this booking
        if ($booking->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot view this booking.');
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking
        ]);
    }

    #[Route('/hotels/{id}/book', name: 'booking_create', methods: ['POST'])]
    public function create(Request $request, int $id): Response
    {
        // Check if user is authenticated
        if (!$this->getUser()) {
            $this->addFlash('error', 'You must be logged in to make a booking.');
            return $this->redirectToRoute('app_login');
        }

        // Debug: Log form data
        $formData = $request->request->all();
        error_log('Form data: ' . json_encode($formData));

        // Validate CSRF token
        $submittedToken = $request->request->get('_token');
        error_log('Submitted token: ' . $submittedToken);
        
        if (!$this->isCsrfTokenValid('book-hotel-' . $id, $submittedToken)) {
            error_log('CSRF token validation failed');
            $this->addFlash('error', 'Invalid form submission.');
            return $this->redirectToRoute('app_hotel_show', ['id' => $id]);
        }

        // Find hotel
        $hotel = $this->hotelRepository->find($id);
        if (!$hotel) {
            $this->addFlash('error', 'Hotel not found.');
            return $this->redirectToRoute('app_hotels');
        }

        try {
            // Parse dates
            $startDate = $request->request->get('start_date');
            $endDate = $request->request->get('end_date');
            error_log("Start date: $startDate, End date: $endDate");
            
            $startDateTime = new \DateTime($startDate);
            $endDateTime = new \DateTime($endDate);
            $numberOfPeople = (int) $request->request->get('number_of_people');
            error_log("Number of people: $numberOfPeople, Max guests: " . $hotel->getMaxGuests());

            // Validate dates
            if ($startDateTime > $endDateTime) {
                throw new \InvalidArgumentException('Check-out date must be after check-in date.');
            }

            if ($startDateTime == $endDateTime) {
                throw new \InvalidArgumentException('Booking must be for at least one night.');
            }

            // Validate number of guests
            if ($numberOfPeople < 1) {
                throw new \InvalidArgumentException('Number of guests must be at least 1.');
            }
            
            if ($numberOfPeople > $hotel->getMaxGuests()) {
                throw new \InvalidArgumentException(sprintf('Maximum %d guests allowed for this hotel.', $hotel->getMaxGuests()));
            }

            // Create booking
            $booking = new Booking();
            $booking->setHotel($hotel);
            $booking->setUser($this->getUser());
            $booking->setStartDate($startDateTime);
            $booking->setEndDate($endDateTime);
            $booking->setNumberOfPeople($numberOfPeople);
            $booking->setStatus('pending');
            
            // Calculate total price
            $nights = $endDateTime->diff($startDateTime)->days;
            $totalPrice = $nights * $hotel->getPricePerNight();
            $booking->setTotalPrice($totalPrice);

            // Save booking
            $this->entityManager->persist($booking);
            $this->entityManager->flush();

            $this->addFlash('success', 'Booking created successfully!');
            return $this->redirectToRoute('app_booking_show', ['id' => $booking->getId()]);

        } catch (\Exception $e) {
            error_log('Booking error: ' . $e->getMessage());
            $this->addFlash('error', 'Error creating booking: ' . $e->getMessage());
            return $this->redirectToRoute('app_hotel_show', ['id' => $id]);
        }
    }

    #[Route('/flights/{id}/book', name: 'booking_create_flight', methods: ['POST'])]
    public function createFlightBooking(Request $request, Flight $flight, EntityManagerInterface $entityManager): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('book-flight-' . $flight->getId(), $submittedToken)) {
            $this->addFlash('error', 'Invalid CSRF token. Please try again.');
            return $this->redirectToRoute('app_flight_show', ['id' => $flight->getId()]);
        }

        $numberOfPassengers = (int) $request->request->get('number_of_passengers', 1);
        
        // Validate number of passengers
        if ($numberOfPassengers < 1 || $numberOfPassengers > $flight->getAvailableSeats()) {
            $this->addFlash('error', 'Invalid number of passengers.');
            return $this->redirectToRoute('app_flight_show', ['id' => $flight->getId()]);
        }

        // Check if flight is still available
        if ($flight->getAvailableSeats() < $numberOfPassengers) {
            $this->addFlash('error', 'Not enough seats available for this flight.');
            return $this->redirectToRoute('app_flight_show', ['id' => $flight->getId()]);
        }

        try {
            $booking = new Booking();
            $booking->setUser($this->getUser());
            $booking->setFlight($flight);
            $booking->setNumberOfPassengers($numberOfPassengers);
            $booking->setNumberOfPeople($numberOfPassengers);
            $booking->setTotalPrice($flight->getPrice() * $numberOfPassengers);
            $booking->setStatus('confirmed');
            $booking->setBookingDate(new \DateTime());
            $booking->setStartDate($flight->getDepartureTime());
            $booking->setEndDate($flight->getArrivalTime());
            $booking->setBookingReference('FLT-' . strtoupper(substr(uniqid(), -6)));

            // Update available seats
            $flight->setAvailableSeats($flight->getAvailableSeats() - $numberOfPassengers);

            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', 'Flight booked successfully!');
            return $this->redirectToRoute('booking_confirmation', ['id' => $booking->getId()]);

        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while booking the flight. Please try again.');
            return $this->redirectToRoute('app_flight_show', ['id' => $flight->getId()]);
        }
    }

    #[Route('/confirmation/{id}', name: 'booking_confirmation')]
    public function confirmation(Booking $booking): Response
    {
        // Check if the current user owns this booking
        if ($booking->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot view this booking.');
        }

        return $this->render('booking/confirmation.html.twig', [
            'booking' => $booking,
        ]);
    }

    #[Route('/packages/{id}/book', name: 'booking_create_package', methods: ['POST'])]
    public function createPackageBooking(Request $request, Package $package, EntityManagerInterface $entityManager): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('book-package-' . $package->getId(), $submittedToken)) {
            $this->addFlash('error', 'Invalid CSRF token. Please try again.');
            return $this->redirectToRoute('app_package_show', ['id' => $package->getId()]);
        }

        $numberOfPeople = (int) $request->request->get('number_of_people', 1);
        $startDate = new \DateTime($request->request->get('start_date'));
        $specialRequests = $request->request->get('special_requests');
        
        // Validate number of people
        if ($numberOfPeople < 1 || $numberOfPeople > $package->getAvailableSpots()) {
            $this->addFlash('error', 'Invalid number of travelers.');
            return $this->redirectToRoute('app_package_show', ['id' => $package->getId()]);
        }

        // Check if package is still available
        if ($package->getAvailableSpots() < $numberOfPeople) {
            $this->addFlash('error', 'Not enough spots available for this package.');
            return $this->redirectToRoute('app_package_show', ['id' => $package->getId()]);
        }

        // Validate travel date
        if ($startDate < $package->getStartDate() || $startDate > $package->getEndDate()) {
            $this->addFlash('error', 'Invalid travel date selected.');
            return $this->redirectToRoute('app_package_show', ['id' => $package->getId()]);
        }

        try {
            $booking = new Booking();
            $booking->setUser($this->getUser());
            $booking->setPackage($package);
            $booking->setNumberOfPeople($numberOfPeople);
            $booking->setTotalPrice($package->getPrice() * $numberOfPeople);
            $booking->setStatus('confirmed');
            $booking->setBookingDate(new \DateTime());
            $booking->setStartDate($startDate);
            $booking->setEndDate((clone $startDate)->modify('+' . $package->getDuration() . ' days'));
            $booking->setSpecialRequests($specialRequests);
            $booking->setBookingReference('PKG-' . strtoupper(substr(uniqid(), -6)));

            // Update available spots
            $package->setAvailableSpots($package->getAvailableSpots() - $numberOfPeople);

            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', 'Package booked successfully!');
            return $this->redirectToRoute('booking_confirmation', ['id' => $booking->getId()]);

        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while booking the package. Please try again.');
            return $this->redirectToRoute('app_package_show', ['id' => $package->getId()]);
        }
    }
}