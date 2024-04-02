<?php

namespace App\Repository;

use App\Entity\EpisodeShow;
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


    public function add(EpisodeShow $entity, bool $flush = false): void
    {

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function remove(EpisodeShow $entity, bool $flush = false): void
    {

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findElementsByDay()
    {

        return $this->createQueryBuilder('e')
            ->select("DATE_FORMAT(e.showDate, '%Y-%m-%d') as day, e as episodes")
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function getDurationByType($type)
    {

        return $this->createQueryBuilder('e')
            ->select('SUM(e.duration) AS COUNT')
            ->leftJoin('e.serie', 's')
            ->andWhere('s.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getDurationBySerie($id)
    {

        return $this->createQueryBuilder('e')
            ->select('SUM(e.duration) AS COUNT')
            ->leftJoin('e.serie', 's')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getDutation()
    {

        return $this->createQueryBuilder('e')
            ->select('SUM(e.duration) AS SUM')
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getDutationByYear($year)
    {

        return $this->createQueryBuilder('e')
            ->select('SUM(e.duration) AS SUM')
            ->andWhere('e.showDate >= :start')
            ->setParameter('start', $year.'-01-01')
            ->andWhere('e.showDate <= :end')
            ->setParameter('end', $year.'-12-31')
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getCountBySerie($id)
    {

        return $this->createQueryBuilder('e')
            ->select('count(e.name) AS COUNT')
            ->leftJoin('e.serie', 's')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @param $serie
     *
     * @return float|int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findBySerie($serie)
    {

        return $this->createQueryBuilder('e')
            ->andWhere('e.serie = :serie')
            ->setParameter('serie', $serie)
            ->andWhere('e.tvdbId IS NOT NULL')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @param $serie
     *
     * @return float|int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findBySerieWitoutTVDB($serie)
    {

        return $this->createQueryBuilder('e')
            ->andWhere('e.serie = :serie')
            ->setParameter('serie', $serie)
            ->andWhere('e.tvdbId IS NULL')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @return float|int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findWitoutTVDB()
    {

        return $this->createQueryBuilder('e')
            ->andWhere('e.tvdbId IS NULL')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param $serie
     *
     * @return float|int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findByDurationNull()
    {

        return $this->createQueryBuilder('e')
            ->andWhere('e.duration IS NULL')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param $serie
     *
     * @return float|int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findBySerieWithTVDB()
    {

        return $this->createQueryBuilder('e')
            ->andWhere('e.tvdbId IS NOT NULL')
            ->andWhere('e.vfName = false')
            ->getQuery()
            ->getResult();
    }


    public function getDurationGenre()
    {

        return $this->createQueryBuilder('e')
            ->select('SUM(e.duration) AS COUNT, g.name AS name')
            ->leftJoin('e.serie', 's')
            ->leftJoin('s.animeGenres', 'g')
            ->andWhere('s.type = :type')
            ->setParameter('type', 'Anime')
            ->groupBy('g.name')
            ->orderBy('COUNT', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function getDurationTheme()
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
            ->getResult();
    }


    /**
     * @param $serie
     *
     * @return float|int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findAnime()
    {

        return $this->createQueryBuilder('e')
            ->leftJoin('e.serie', 's')
            ->andWhere('s.type = :type')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findSerie()
    {

        return $this->createQueryBuilder('e')
            ->leftJoin('e.serie', 's')
            ->andWhere('s.type = :type')
            ->setParameter('type', 'Series')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findReplay()
    {

        return $this->createQueryBuilder('e')
            ->leftJoin('e.serie', 's')
            ->andWhere('s.type = :type')
            ->setParameter('type', 'Replay')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findMonth()
    {

        return $this->createQueryBuilder('e')
            ->select("DATE_FORMAT(e.showDate, '%Y-%m') AS DATE, SUM(e.duration) AS DURATION, s.type AS TYPE")
            ->leftJoin('e.serie', 's')
            ->addGroupBy("DATE")
            ->addGroupBy("TYPE")
            ->getQuery()
            ->getResult();
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findByDate(string $year, string $month)
    {

        return $this->createQueryBuilder('e')
            ->andWhere('e.showDate LIKE :date')
            ->setParameter('date', $year.'-'.$month.'-%')
            ->getQuery()
            ->getResult();
    }

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
    