<?php

namespace App\Repository;

use App\Entity\EpisodeShow;
use App\Entity\Serie;
use App\Entity\SerieType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
    public function findMonth(): mixed
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
    public function findBySerieType(SerieType $type): mixed
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
    public function findByDate(string $year, string $month): mixed
    {

        $param = $year.'-%';

        if ($month = 0){
            $param = $year.'-'.$month.'-%';
        }

        return $this->createQueryBuilder('es')
            ->andWhere('es.showDate LIKE :date')
            ->setParameter('date', $param)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return float|int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findLastEpisodeBySerie(Serie $serie): mixed
    {

        return $this->createQueryBuilder('es')
            ->leftJoin('es.episode', 'e')
            ->andWhere('e.serie = :serie')
            ->setParameter('serie', $serie)
            ->orderBy('es.showDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function getEpisodesBySerie(Serie $serie): mixed
    {

        return $this->createQueryBuilder('es')
            ->leftJoin('es.episode', 'e')
            ->andWhere('e.serie = :serie')
            ->setParameter('serie', $serie)
            ->orderBy('es.showDate', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function getDutation(): mixed
    {

        return $this->createQueryBuilder('es')
            ->leftJoin('es.episode', 'e')
            ->select('SUM(e.duration) AS SUM')
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getDutationByYear(string $year): mixed
    {

        return $this->createQueryBuilder('es')
            ->leftJoin('es.episode', 'e')
            ->select('SUM(e.duration) AS SUM')
            ->andWhere('es.showDate >= :start')
            ->setParameter('start', $year.'-01-01')
            ->andWhere('es.showDate <= :end')
            ->setParameter('end', $year.'-12-31')
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getDurationBySerie(int $id): mixed
    {

        return $this->createQueryBuilder('es')
            ->select('SUM(e.duration) AS COUNT')
            ->leftJoin('es.episode', 'e')
            ->leftJoin('e.serie', 's')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getCountBySerie(int $id): mixed
    {

        return $this->createQueryBuilder('es')
            ->select('count(es.id) AS COUNT')
            ->leftJoin('es.episode', 'e')
            ->leftJoin('e.serie', 's')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
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
