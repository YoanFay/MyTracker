<?php

namespace App\Repository;

use App\Entity\MangaTome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MangaTome>
 *
 * @method MangaTome|null find($id, $lockMode = null, $lockVersion = null)
 * @method MangaTome|null findOneBy(array $criteria, array $orderBy = null)
 * @method MangaTome[]    findAll()
 * @method MangaTome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MangaTomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MangaTome::class);
    }

    public function getTomeGenre()
    {
        return $this->createQueryBuilder('mt')
            ->select('COUNT(mt.id) AS COUNT, g.name AS name')
            ->leftJoin('mt.manga', 'm')
            ->leftJoin('m.mangaGenre', 'g')
            ->groupBy('g.name')
            ->orderBy('COUNT', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getTomeCountInfo($manga)
    {
        return $this->createQueryBuilder('mt')
            ->select('COUNT(mt.id) AS tomeRelease, COUNT(mt.readingEndDate) AS tomeRead')
            ->andWhere('mt.manga = :manga')
            ->setParameter('manga', $manga)
            ->andWhere('mt.releaseDate <= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getTomeRead()
    {
        return $this->createQueryBuilder('mt')
            ->select('COUNT(mt.readingEndDate) AS COUNT')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getTomeReadByYear($year)
    {
        return $this->createQueryBuilder('mt')
            ->select('COUNT(mt.readingEndDate) AS COUNT')
            ->andWhere('mt.readingEndDate >= :start')
            ->setParameter('start', $year.'-01-01')
            ->andWhere('mt.readingEndDate <= :end')
            ->setParameter('end', $year.'-12-31')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getTomeStart()
    {
        return $this->createQueryBuilder('mt')
            ->select('COUNT(mt.readingStartDate) AS COUNT')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getTomeStartByYear($year)
    {
        return $this->createQueryBuilder('mt')
            ->select('COUNT(mt.readingStartDate) AS COUNT')
            ->andWhere('mt.readingStartDate >= :start')
            ->setParameter('start', $year.'-01-01')
            ->andWhere('mt.readingStartDate <= :end')
            ->setParameter('end', $year.'-12-31')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


    /**
     * @throws NonUniqueResultException
     */
    public function getFirstCover($manga)
    {
        return $this->createQueryBuilder('mt')
            ->select('mt.cover AS COVER')
            ->andWhere('mt.manga = :manga')
            ->andWhere('mt.tomeNumber = 1')
            ->setParameter('manga', $manga)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


    /**
     * @throws NonUniqueResultException
     */
    public function getCurrentTome($manga){

        return $this->createQueryBuilder('mt')
            ->andWhere('mt.manga = :manga')
            ->setParameter('manga', $manga)
            ->andWhere('mt.readingEndDate IS NULL')
            ->orderBy('mt.tomeNumber')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;

    }

    /*public function getTomeTheme()
    {
        return $this->createQueryBuilder('e')
            ->select('SUM(e.duration) AS COUNT, t.name AS name')
            ->leftJoin('e.serie', 's')
            ->leftJoin('s.animeThemes', 't')
            ->andWhere('s.type = :type')
            ->setParameter('type', 'Anime')
            ->groupBy('t.name')
            ->orderBy('COUNT', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }*/

//    /**
//     * @return MangaTome[] Returns an array of MangaTome objects
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

//    public function findOneBySomeField($value): ?MangaTome
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
