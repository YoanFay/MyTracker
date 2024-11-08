<?php

namespace App\Repository;

use App\Entity\Episode;
use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Episode>
 *
 * @method Episode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Episode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Episode[]    findAll()
 * @method Episode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, Episode::class);
    }


    public function add(Episode $entity, bool $flush = false): void
    {

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function remove(Episode $entity, bool $flush = false): void
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


    public function getDurationByType()
    {

        return $this->createQueryBuilder('e')
            ->select('SUM(e.duration) AS COUNT, t.name AS TYPE')
            ->leftJoin('e.serie', 's')
            ->leftJoin('s.serieType', 't')
            ->groupBy('TYPE')
            ->getQuery()
            ->getResult();
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
            ->leftJoin('s.serieType', 't')
            ->andWhere('t.name = :type')
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
            ->leftJoin('s.serieType', 'ty')
            ->andWhere('ty.name = :type')
            ->setParameter('type', 'Anime')
            ->groupBy('t.name')
            ->orderBy('COUNT', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findByType($type)
    {

        return $this->createQueryBuilder('e')
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

        return $this->createQueryBuilder('e')
            ->andWhere('e.showDate LIKE :date')
            ->setParameter('date', $year.'-'.$month.'-%')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findByDateNotNull()
    {

        return $this->createQueryBuilder('e')
            ->andWhere('e.showDate IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Episode
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
    