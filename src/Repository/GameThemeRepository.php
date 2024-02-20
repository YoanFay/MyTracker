<?php

namespace App\Repository;

use App\Entity\GameTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameTheme>
 *
 * @method GameTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameTheme[]    findAll()
 * @method GameTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTheme::class);
    }

//    /**
//     * @return GameTheme[] Returns an array of GameTheme objects
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

//    public function findOneBySomeField($value): ?GameTheme
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
