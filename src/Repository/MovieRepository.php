<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, Movie::class);
    }


    public function add(Movie $entity, bool $flush = false): void
    {

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function remove(Movie $entity, bool $flush = false): void
    {

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function getNoArtwork():mixed
    {

        return $this->createQueryBuilder('m')
            ->andWhere('m.artwork IS NULL')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return float|int|mixed|string|null
     */
    public function findByDateNotNull()
    {

        return $this->createQueryBuilder('m')
            ->andWhere('m.showDate IS NOT NULL')
            ->getQuery()
            ->getResult();
    }


    public function getByLikeName(?string $text):mixed
    {


        $qb = $this->createQueryBuilder('m');

        if ($text) {
            $qb
                ->andWhere('m.name LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        return $qb
            ->getQuery()
            ->getResult();

    }

//    /**
//     * @return Movie[] Returns an array of Movie objects
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

//    public function findOneBySomeField($value): ?Movie
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
