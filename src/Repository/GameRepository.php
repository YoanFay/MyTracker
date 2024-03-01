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
    public function findAllFilter($sort, $order): array
    {

        return $this->createQueryBuilder('g')
            ->orderBy('g.'.$sort, $order)
            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @return Game[] Returns an array of Game
     */
    public function findGameProgress(): array
    {

        return $this->createQueryBuilder('g')
            ->leftJoin('g.gameTrackers', 't')
            ->andWhere('t.startDate IS NOT NULL')
            ->andWhere('t.endDate IS NULL')
            ->orderBy('g.name')
            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @return Game[] Returns an array of Game
     */
    public function findGameEnd(): array
    {

        return $this->createQueryBuilder('g')
            ->leftJoin('g.gameTrackers', 't')
            ->andWhere('t.endDate IS NOT NULL')
            ->orderBy('g.name')
            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @return Game[] Returns an array of Game
     */
    public function findGameNotStart(): array
    {

        return $this->createQueryBuilder('g')
            ->leftJoin('g.gameTrackers', 't')
            ->andWhere('t.startDate IS NULL')
            ->orderBy('g.name')
            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @return Game[] Returns an array of Game
     */
    public function findGameNotSerie(): array
    {

        return $this->createQueryBuilder('g')
            ->andWhere('g.serie IS NULL')
            ->getQuery()
            ->getResult()
            ;

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
