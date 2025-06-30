<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, Game::class);
    }


    /**
     * @return Game[] Returns an array of Game
     */
    public function findAllFilter(?string $text): array
    {

        $qb = $this->createQueryBuilder('g');


        if ($text) {
            $qb
                ->andWhere('g.name LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        return $qb
            ->orderBy('g.name')
            ->getQuery()
            ->getResult();

    }


    /**
     * @return Game[] Returns an array of Game
     */
    public function findGameProgress(?string $text): array
    {

        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.gameTrackers', 't');


        if ($text) {
            $qb
                ->andWhere('g.name LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        return $qb
            ->andWhere('t.startDate IS NOT NULL')
            ->andWhere('t.endDate IS NULL')
            ->orderBy('g.name')
            ->getQuery()
            ->getResult();

    }


    /**
     * @return Game[] Returns an array of Game
     */
    public function findGameEnd(?string $text): array
    {

        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.gameTrackers', 't');


        if ($text) {
            $qb
                ->andWhere('g.name LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        return $qb
            ->andWhere('t.endDate IS NOT NULL')
            ->andWhere('t.completeDate IS NULL')
            ->orderBy('g.name')
            ->getQuery()
            ->getResult();

    }


    /**
     * @return Game[] Returns an array of Game
     */
    public function findGameFullEnd(?string $text): array
    {

        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.gameTrackers', 't');


        if ($text) {
            $qb
                ->andWhere('g.name LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        return $qb
            ->andWhere('t.completeDate IS NOT NULL')
            ->orderBy('g.name')
            ->getQuery()
            ->getResult();

    }


    /**
     * @return Game[] Returns an array of Game
     */
    public function findGameNotStart(?string $text): array
    {

        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.gameTrackers', 't');


        if ($text) {
            $qb
                ->andWhere('g.name LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        return $qb
            ->andWhere('t.startDate IS NULL')
            ->orderBy('g.name')
            ->getQuery()
            ->getResult();

    }


    /**
     * @return Game[] Returns an array of Game
     */
    public function findGameNotSerie(): array
    {

        return $this->createQueryBuilder('g')
            ->andWhere('g.serie IS NULL')
            ->getQuery()
            ->getResult();

    }


    /**
     * @return Game[] Returns an array of Game
     */
    public function countGameEnd(): array
    {

        return $this->createQueryBuilder('g')
            ->select('COUNT(t.endDate) AS COUNT')
            ->leftJoin('g.gameTrackers', 't')
            ->getQuery()
            ->getOneOrNullResult();

    }


    /**
     * @return Game[] Returns an array of Game
     */
    public function countGameEndByYear(int $year): array
    {

        return $this->createQueryBuilder('g')
            ->select('COUNT(t.endDate) AS COUNT')
            ->leftJoin('g.gameTrackers', 't')
            ->andWhere('t.endDate >= :start')
            ->setParameter('start', $year.'-01-01')
            ->andWhere('t.endDate <= :end')
            ->setParameter('end', $year.'-12-31')
            ->getQuery()
            ->getOneOrNullResult();

    }


    /**
     * @return Game[] Returns an array of Game
     */
    public function countGameFullEnd(): array
    {

        return $this->createQueryBuilder('g')
            ->select('COUNT(t.completeDate) AS COUNT')
            ->leftJoin('g.gameTrackers', 't')
            ->getQuery()
            ->getOneOrNullResult();

    }


    /**
     * @return Game[] Returns an array of Game
     */
    public function countGameFullEndByYear(int $year): array
    {

        return $this->createQueryBuilder('g')
            ->select('COUNT(t.completeDate) AS COUNT')
            ->leftJoin('g.gameTrackers', 't')
            ->andWhere('t.completeDate >= :start')
            ->setParameter('start', $year.'-01-01')
            ->andWhere('t.completeDate <= :end')
            ->setParameter('end', $year.'-12-31')
            ->getQuery()
            ->getOneOrNullResult();

    }

//    /**
//     * @return Game[] Returns an array of Game objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Game
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
