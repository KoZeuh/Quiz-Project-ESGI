<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use App\Entity\User;

class UserCrudController extends AbstractCrudController
{

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['prenom', 'nom', 'email'])
            ->setPaginatorPageSize(20)
            ->setPaginatorRangeSize(4)
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true)
            ->setEntityPermission('ROLE_ADMIN');
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('prenom')
            ->add('nom')
            ->add('email')
            ->add('promotions')
            ->add('roles')
        ;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('prenom'),
            TextField::new('nom'),
            EmailField::new('email'),
            TextField::new('password')->hideOnIndex()->hideWhenUpdating(),
            DateTimeField::new('createdAt')->hideOnForm(),
            AssociationField::new('promotions'),
            ChoiceField::new('roles')->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Formateur' => 'ROLE_FORMATEUR',
                'Admin' => 'ROLE_ADMIN',
            ])->renderAsBadges([
                'ROLE_USER' => 'primary',
                'ROLE_FORMATEUR' => 'warning',
                'ROLE_ADMIN' => 'success',
            ])->renderExpanded()->allowMultipleChoices()
        ];
    }
}
