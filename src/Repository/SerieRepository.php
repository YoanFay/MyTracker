<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 *
 * @method Serie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Serie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Serie[]    findAll()
 * @method Serie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function add(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function findNotTvdbId(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.tvdbId IS NULL')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function findTvdbId(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.tvdbId IS NOT NULL')
            ->andWhere('s.vfName = false')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function findArtworkId(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.artwork IS NULL')
            ->andWhere('s.tvdbId IS NOT NULL')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function findNoGenre(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.genres', 'g')
            ->andWhere('g.id IS NULL')
            ->andWhere('s.tvdbId IS NOT NULL')
            ->andWhere('s.type <> :type')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function findAnime(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.type = :type')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult()
            ;
    }

//    public function findOneBySomeField($value): ?Serie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
