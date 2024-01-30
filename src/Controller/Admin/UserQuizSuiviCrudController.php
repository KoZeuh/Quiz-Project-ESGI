<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use App\Entity\UserQuizSuivi;

class UserQuizSuiviCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserQuizSuivi::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('user')
            ->add('quiz')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize(20)
            ->setPaginatorRangeSize(4)
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true);
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
    }
}
