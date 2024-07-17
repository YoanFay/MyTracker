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
            ->leftJoin('s.artwork', 'a')
            ->andWhere('a.language <> "eng" AND a.language <> "fra"')
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
            ->leftJoin('s.serieType', 't')
            ->andWhere('g.id IS NULL')
            ->andWhere('s.tvdbId IS NOT NULL')
            ->andWhere('t.name <> :type')
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
            ->leftJoin('s.serieType', 't')
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function noFirstAired(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.firstAired IS NULL')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function updateAired(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nextAired IS NULL OR s.nextAired < CURRENT_DATE()')
            ->andWhere('s.status <> :status OR s.status IS NULL')
            ->setParameter('status', "Ended")
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function ended(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.status = :status')
            ->setParameter('status', "Ended")
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function noThemeGenre(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.animeGenres is EMPTY or s.animeThemes is EMPTY')
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function serieByGenre($animeGenre): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.animeGenres IN (:idGenre)')
            ->setParameter('idGenre', $animeGenre)
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function noCompanies(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.company is EMPTY')
            ->andWhere('s.tvdbId IS NOT NULL')
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
