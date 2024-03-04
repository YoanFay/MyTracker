<?php

namespace App\Repository;

use App\Entity\Manga;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Manga>
 *
 * @method Manga|null find($id, $lockMode = null, $lockVersion = null)
 * @method Manga|null findOneBy(array $criteria, array $orderBy = null)
 * @method Manga[]    findAll()
 * @method Manga[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MangaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manga::class);
    }

    /**
     * @return Manga[] Returns an array of Manga objects
     */
    public function getCountOfReadingTomesPerManga()
    {
        return $this->createQueryBuilder('m')
            ->select('m AS info, COUNT(t.readingEndDate) AS tomeCount')
            ->leftJoin('m.mangaTomes', 't')
            ->groupBy('m.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Manga[] Returns an array of Manga objects
     */
    public function getMangaTomeByGenre()
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(t.id) as COUNT, g.name as name')
            ->leftJoin('m.mangaTomes', 't')
            ->leftJoin('m.genres', 'g')
            ->andWhere('t.endDate IS NOT NULL')
            ->groupBy('name')
            ->orderBy('COUNT', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Manga[] Returns an array of Manga objects
     */
    public function getMangaTomeByTheme()
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(t.id) as COUNT, th.name as name')
            ->leftJoin('m.mangaTomes', 't')
            ->leftJoin('m.themes', 'th')
            ->andWhere('t.endDate IS NOT NULL')
            ->groupBy('name')
            ->orderBy('COUNT', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Manga[] Returns an array of Manga objects
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

//    public function findOneBySomeField($value): ?Manga
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
