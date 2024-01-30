<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;


use App\Entity\Quiz;
use App\Entity\Promotion;


class QuizCrudController extends AbstractCrudController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->getUser();
        $userId = $user ? $user->getId() : null;

        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $queryBuilder
                ->join('entity.promotions', 'promotion')
                ->join('promotion.users', 'user')
                ->andWhere('user.id = :userId')
                ->setParameter('userId', $userId);
        }

        return $queryBuilder;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('libelle')
            ->add('categorie')
            ->add('questions')
            ->add('promotions')
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

    public static function getEntityFqcn(): string
    {
        return Quiz::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $promotions = AssociationField::new('promotions');
        $userRoles = $this->getUser()->getRoles();

        if (!in_array('ROLE_ADMIN', $userRoles)) {
            $promotions = AssociationField::new('promotions')->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                $queryBuilder->select('promotion')
                    ->from(Promotion::class, 'promotion')
                    ->join('promotion.users', 'user')
                    ->where('user.id = :user')
                    ->setParameter('user', $this->getUser()->getId());

            });
        }
        
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('categorie'),
            TextField::new('libelle'),
            IntegerField::new('duration'),
            $promotions,
            AssociationField::new('questions'),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
}
