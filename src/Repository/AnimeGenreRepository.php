<?php

namespace App\Repository;

use App\Entity\AnimeGenre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnimeGenre>
 *
 * @method AnimeGenre|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnimeGenre|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnimeGenre[]    findAll()
 * @method AnimeGenre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimeGenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnimeGenre::class);
    }

//    /**
//     * @return AnimeGenre[] Returns an array of AnimeGenre objects
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

//    public function findOneBySomeField($value): ?AnimeGenre
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
