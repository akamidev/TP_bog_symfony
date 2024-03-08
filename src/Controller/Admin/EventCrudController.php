<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', '📆 All events')
            ->setEntityLabelInSingular('Event')
            ->setEntityLabelInPlural('Events')
            ->setSearchFields(['name'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Event Information'),
            TextField::new('name')
                ->setLabel('🚀 Event Name')
                ->setHelp('The name of the event'),
            TextField::new('location')
                ->setLabel('📍 Location')
                ->setHelp('Where the event is located?'),
            TextField::new('theme')->hideOnIndex()
                ->setLabel('🎨 Theme')
                ->setHelp('What is the theme of the event?'),
            MoneyField::new('price')->setCurrency('EUR')->hideOnIndex()
                ->setLabel('💶 Price')
                ->setHelp('Set the price of the event'),
            AssociationField::new('speakers')->hideOnIndex()
                ->setLabel('📢 Speakers')
                ->setHelp('Who will be speaking at the event?')
                ->setFormTypeOption('choice_label', 'firstname'),
            DateTimeField::new('date')
                ->setLabel('📅 Date')
                ->setFormat('dd/MM/yyyy')
                ->setHelp('When the event will happen?'),
            IntegerField::new('attendee')->hideOnIndex()
                ->setLabel('👥 Attendee')
                ->setHelp('How many people are attending?'),
        ];
    }
} // Do not write anything after this line
