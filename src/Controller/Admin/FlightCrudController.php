<?php

namespace App\Controller\Admin;

use App\Entity\Flight;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class FlightCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Flight::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Flight')
            ->setEntityLabelInPlural('Flights')
            ->setSearchFields(['flightNumber', 'airline', 'origin', 'destination'])
            ->setDefaultSort(['departureTime' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('flightNumber')
            ->setHelp('Unique flight identifier (e.g., AA123)');

        yield TextField::new('airline');

        yield TextField::new('origin')
            ->setHelp('Departure airport code (e.g., JFK)');

        yield TextField::new('destination')
            ->setHelp('Arrival airport code (e.g., LAX)');

        yield DateTimeField::new('departureTime');
        yield DateTimeField::new('arrivalTime');

        yield MoneyField::new('price')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);

        yield IntegerField::new('availableSeats')
            ->setLabel('Available Seats');

        yield ChoiceField::new('status')
            ->setChoices([
                'Scheduled' => 'scheduled',
                'Delayed' => 'delayed',
                'Boarding' => 'boarding',
                'Departed' => 'departed',
                'Arrived' => 'arrived',
                'Cancelled' => 'cancelled'
            ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('fa fa-plus')->addCssClass('btn btn-success');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash');
            });
    }
}
