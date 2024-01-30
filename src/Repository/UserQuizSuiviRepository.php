<?php

namespace App\Repository;

use App\Entity\UserQuizSuivi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserQuizSuivi>
 *
 * @method UserQuizSuivi|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserQuizSuivi|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserQuizSuivi[]    findAll()
 * @method UserQuizSuivi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserQuizSuiviRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserQuizSuivi::class);
    }

//    /**
//     * @return UserQuizSuivi[] Returns an array of UserQuizSuivi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserQuizSuivi
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
