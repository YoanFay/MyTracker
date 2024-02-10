<?php

namespace App\Repository;

use App\Entity\MangaTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MangaTheme>
 *
 * @method MangaTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method MangaTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method MangaTheme[]    findAll()
 * @method MangaTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MangaThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MangaTheme::class);
    }

//    /**
//     * @return MangaTheme[] Returns an array of MangaTheme objects
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

//    public function findOneBySomeField($value): ?MangaTheme
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
