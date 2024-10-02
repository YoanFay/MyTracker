<?php

namespace App\Repository;

use App\Entity\MovieShow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MovieShow>
 *
 * @method MovieShow|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovieShow|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovieShow[]    findAll()
 * @method MovieShow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieShowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, MovieShow::class);
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findMonth()
    {

        return $this->createQueryBuilder('ms')
            ->select("DATE_FORMAT(ms.showDate, '%Y-%m') AS DATE, SUM(m.duration) AS DURATION")
            ->leftJoin('ms.movie', 'm')
            ->addGroupBy("DATE")
            ->getQuery()
            ->getResult();
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findLastShowByMovie($movie)
    {

        return $this->createQueryBuilder('ms')
            ->leftJoin('ms.movie', 'm')
            ->andWhere('m = :movie')
            ->setParameter('movie', $movie)
            ->orderBy('ms.showDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

    }

//    /**
//     * @return MovieShow[] Returns an array of MovieShow objects
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

//    public function findOneBySomeField($value): ?MovieShow
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
