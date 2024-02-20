<?php

namespace App\Repository;

use App\Entity\GameMode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameMode>
 *
 * @method GameMode|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameMode|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameMode[]    findAll()
 * @method GameMode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameModeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameMode::class);
    }

//    /**
//     * @return GameMode[] Returns an array of GameMode objects
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

//    public function findOneBySomeField($value): ?GameMode
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
