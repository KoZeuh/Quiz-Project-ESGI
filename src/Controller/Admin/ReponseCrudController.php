<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use Doctrine\ORM\QueryBuilder;

use App\Entity\Reponse;

class ReponseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reponse::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('libelle')
            ->add('question')
            ->add('users')
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->getUser();
        $userId = $user ? $user->getId() : null;

        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $queryBuilder
                ->join('entity.question', 'question')
                ->join('question.quiz', 'quiz')
                ->join('quiz.promotions', 'promotion')
                ->join('promotion.users', 'user')
                ->andWhere('user.id = :userId')
                ->setParameter('userId', $userId);
        }

        return $queryBuilder;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['libelle', 'question'])
            ->setPaginatorPageSize(20)
            ->setPaginatorRangeSize(4)
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true);
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('libelle'),
            AssociationField::new('question'),
            BooleanField::new('bonne_reponse'),
            AssociationField::new('users')->onlyWhenUpdating()->onlyOnIndex(),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
}
