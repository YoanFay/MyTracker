<?php

namespace App\Repository;

use App\Entity\EpisodeShow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EpisodeShow>
 *
 * @method EpisodeShow|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpisodeShow|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpisodeShow[]    findAll()
 * @method EpisodeShow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeShowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, EpisodeShow::class);
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findMonth()
    {

        return $this->createQueryBuilder('es')
            ->select("DATE_FORMAT(es.showDate, '%Y-%m') AS DATE, SUM(e.duration) AS DURATION, t.name AS TYPE")
            ->leftJoin('es.episode', 'e')
            ->leftJoin('e.serie', 's')
            ->leftJoin('s.serieType', 't')
            ->addGroupBy("DATE")
            ->addGroupBy("TYPE")
            ->getQuery()
            ->getResult();
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findBySerieType($type)
    {

        return $this->createQueryBuilder('es')
            ->leftJoin('es.episode', 'e')
            ->leftJoin('e.serie', 's')
            ->andWhere('s.serieType = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findByDate(string $year, string $month)
    {

        return $this->createQueryBuilder('es')
            ->andWhere('es.showDate LIKE :date')
            ->setParameter('date', $year.'-'.$month.'-%')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return EpisodeShow[] Returns an array of EpisodeShow objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EpisodeShow
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
