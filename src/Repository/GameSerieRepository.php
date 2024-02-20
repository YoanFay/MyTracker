<?php

namespace App\Repository;

use App\Entity\GameSerie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameSerie>
 *
 * @method GameSerie|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameSerie|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameSerie[]    findAll()
 * @method GameSerie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameSerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameSerie::class);
    }

//    /**
//     * @return GameSerie[] Returns an array of GameSerie objects
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

//    public function findOneBySomeField($value): ?GameSerie
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
