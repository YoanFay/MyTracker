<?php

namespace App\Repository;

use App\Entity\GameTracker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameTracker>
 *
 * @method GameTracker|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameTracker|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameTracker[]    findAll()
 * @method GameTracker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameTrackerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTracker::class);
    }

//    /**
//     * @return GameTracker[] Returns an array of GameTracker objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GameTracker
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
