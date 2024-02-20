<?php

namespace App\Repository;

use App\Entity\GameDeveloper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameDeveloper>
 *
 * @method GameDeveloper|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameDeveloper|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameDeveloper[]    findAll()
 * @method GameDeveloper[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameDeveloperRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameDeveloper::class);
    }

//    /**
//     * @return GameDeveloper[] Returns an array of GameDeveloper objects
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

//    public function findOneBySomeField($value): ?GameDeveloper
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
