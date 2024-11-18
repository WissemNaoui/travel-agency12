<?php

namespace App\Controller\Admin;

use App\Entity\Package;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class PackageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Package::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Travel Package')
            ->setEntityLabelInPlural('Travel Packages')
            ->setSearchFields(['name', 'description', 'destination'])
            ->setDefaultSort(['startDate' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
       /* yield TextField::new('name')
            ->setHelp('The name of the travel package');
            
        yield TextEditorField::new('description')
            ->hideOnIndex();
            
        yield TextField::new('destination');
            
        yield MoneyField::new('price')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);
            
        yield IntegerField::new('duration')
            ->setHelp('Duration in days');
            
        yield IntegerField::new('capacity')
            ->setLabel('Max Participants');
            
        yield DateTimeField::new('startDate');
        yield DateTimeField::new('endDate');
            
        yield IntegerField::new('availableSpots')
            ->onlyOnIndex();*/

            return[
            TextField::new('name', 'Package Name'),
            TextareaField::new('description', 'Description'),
            MoneyField::new('price', 'Price')->setCurrency('USD'),
            IntegerField::new('duration', 'Duration (days)'),
            TextField::new('destination', 'Destination'),
            TextField::new('country', 'Country'),
            IntegerField::new('maxParticipants', 'Max Participants'),
            IntegerField::new('availableSpots', 'Available Spots'),
            DateTimeField::new('startDate', 'Start Date'),
            DateTimeField::new('endDate', 'End Date'),
            ArrayField::new('includedServices', 'Included Services'),
            ArrayField::new('highlights', 'Highlights'),
            TextField::new('status', 'Status'),];
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
