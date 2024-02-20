<?php

namespace App\Repository;

use App\Entity\GamePublishers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GamePublishers>
 *
 * @method GamePublishers|null find($id, $lockMode = null, $lockVersion = null)
 * @method GamePublishers|null findOneBy(array $criteria, array $orderBy = null)
 * @method GamePublishers[]    findAll()
 * @method GamePublishers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GamePublishersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GamePublishers::class);
    }

//    /**
//     * @return GamePublishers[] Returns an array of GamePublishers objects
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

//    public function findOneBySomeField($value): ?GamePublishers
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
