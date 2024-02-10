<?php

namespace App\Repository;

use App\Entity\MangaGenre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MangaGenre>
 *
 * @method MangaGenre|null find($id, $lockMode = null, $lockVersion = null)
 * @method MangaGenre|null findOneBy(array $criteria, array $orderBy = null)
 * @method MangaGenre[]    findAll()
 * @method MangaGenre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MangaGenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MangaGenre::class);
    }

//    /**
//     * @return MangaGenre[] Returns an array of MangaGenre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MangaGenre
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
