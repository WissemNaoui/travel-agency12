<?php

namespace App\Command;

use App\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-sample-data',
    description: 'Load sample data for the travel agency',
)]
class LoadSampleDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $packages = [
            [
                'name' => 'Paris Getaway',
                'description' => 'Experience the magic of Paris with this romantic getaway package. Visit the iconic Eiffel Tower, explore the Louvre Museum, and enjoy authentic French cuisine.',
                'price' => 1299.99,
                'duration' => 5,
                'destination' => 'Paris, France',
                'capacity' => 20,
                'startDate' => new \DateTime('2024-06-15'),
                'endDate' => new \DateTime('2024-06-20'),
            ],
            [
                'name' => 'Tokyo Adventure',
                'description' => 'Immerse yourself in Japanese culture with this exciting Tokyo package. Visit ancient temples, experience modern technology, and taste authentic Japanese dishes.',
                'price' => 1899.99,
                'duration' => 7,
                'destination' => 'Tokyo, Japan',
                'capacity' => 15,
                'startDate' => new \DateTime('2024-07-01'),
                'endDate' => new \DateTime('2024-07-08'),
            ],
            [
                'name' => 'Greek Islands Cruise',
                'description' => 'Sail through the beautiful Greek Islands, visiting Santorini, Mykonos, and more. Enjoy crystal clear waters, ancient ruins, and Mediterranean cuisine.',
                'price' => 2499.99,
                'duration' => 10,
                'destination' => 'Greek Islands',
                'capacity' => 30,
                'startDate' => new \DateTime('2024-08-10'),
                'endDate' => new \DateTime('2024-08-20'),
            ],
            [
                'name' => 'Safari Adventure',
                'description' => 'Experience the wildlife of Africa with this amazing safari package. See the Big Five, stay in luxury lodges, and witness breathtaking sunsets.',
                'price' => 3299.99,
                'duration' => 8,
                'destination' => 'Kenya',
                'capacity' => 12,
                'startDate' => new \DateTime('2024-09-05'),
                'endDate' => new \DateTime('2024-09-13'),
            ],
            [
                'name' => 'Bali Paradise',
                'description' => 'Relax in the tropical paradise of Bali. Enjoy pristine beaches, visit ancient temples, and experience traditional Balinese culture.',
                'price' => 1599.99,
                'duration' => 6,
                'destination' => 'Bali, Indonesia',
                'capacity' => 25,
                'startDate' => new \DateTime('2024-10-01'),
                'endDate' => new \DateTime('2024-10-07'),
            ],
            [
                'name' => 'New York City Explorer',
                'description' => 'Discover the Big Apple with this comprehensive NYC package. Visit Times Square, Central Park, Broadway shows, and world-class museums.',
                'price' => 1799.99,
                'duration' => 5,
                'destination' => 'New York, USA',
                'capacity' => 20,
                'startDate' => new \DateTime('2024-11-15'),
                'endDate' => new \DateTime('2024-11-20'),
            ],
        ];

        foreach ($packages as $packageData) {
            $package = new Package();
            $package->setName($packageData['name']);
            $package->setDescription($packageData['description']);
            $package->setPrice($packageData['price']);
            $package->setDuration($packageData['duration']);
            $package->setDestination($packageData['destination']);
            $package->setCapacity($packageData['capacity']);
            $package->setStartDate($packageData['startDate']);
            $package->setEndDate($packageData['endDate']);
            $package->setIsAvailable(true);

            $this->entityManager->persist($package);
        }

        $this->entityManager->flush();

        $io->success('Sample packages have been loaded successfully!');

        return Command::SUCCESS;
    }
}
