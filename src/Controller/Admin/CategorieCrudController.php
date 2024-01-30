<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

use App\Entity\Categorie;

class CategorieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('libelle')
            ->add('quizzes')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['libelle'])
            ->setPaginatorPageSize(20)
            ->setPaginatorRangeSize(4)
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true);
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('libelle');
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield AssociationField::new('quizzes')->setPermission('ROLE_ADMIN');
    }
}
