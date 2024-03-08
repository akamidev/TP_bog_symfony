<?php

namespace App\Controller\Admin;

use App\Entity\Speaker;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class SpeakerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Speaker::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', '📢 All speakers')
            ->setEntityLabelInSingular('Speaker')
            ->setEntityLabelInPlural('Speakers')
            ->setSearchFields(['firstname', 'lastname'])
            ->setDefaultSort(['firstname' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Speaker Information'),
            TextField::new('firstname')
                ->setLabel('👤 First Name')
                ->setHelp('Set the first name of the speaker'),
            TextField::new('lastname')
                ->setLabel('👤 Last Name')
                ->setHelp('Set the last name of the speaker'),
            TextField::new('job')
                ->setLabel('💼 Job')
                ->setHelp('Set the job of the speaker')
                    ->hideOnIndex(),
            TextField::new('company')
                ->setLabel('🏢 Company')
                ->setHelp('What is the company of the speaker?')
                    ->hideOnIndex(),
            IntegerField::new('experience')
                ->setLabel('📅 Experience')
                ->setHelp('How many years of experience?')
                    ->hideOnIndex(),
            ImageField::new('image')
                ->setLabel('📷 Image')
                ->setHelp('Choose an image for the speaker')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setUploadDir('public/uploads/speakers')
                ->setBasePath('uploads/speakers'),
            AssociationField::new('event')
                ->setLabel('📅 Event')
                ->setHelp('Which event is the speaker associated?')
                    ->hideOnIndex(),
        ];
    }
}
