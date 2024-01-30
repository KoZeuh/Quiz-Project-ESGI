<?php

namespace App\Repository;

use App\Entity\QuizPromotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuizPromotion>
 *
 * @method QuizPromotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizPromotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizPromotion[]    findAll()
 * @method QuizPromotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizPromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizPromotion::class);
    }

//    /**
//     * @return QuizPromotion[] Returns an array of QuizPromotion objects
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

//    public function findOneBySomeField($value): ?QuizPromotion
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
