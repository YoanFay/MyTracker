<?php

namespace App\Repository;

use App\Entity\AnimeTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnimeTheme>
 *
 * @method AnimeTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnimeTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnimeTheme[]    findAll()
 * @method AnimeTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimeThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnimeTheme::class);
    }

//    /**
//     * @return AnimeTheme[] Returns an array of AnimeTheme objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AnimeTheme
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
