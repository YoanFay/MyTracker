<?php

namespace App\Repository;

use App\Entity\AnimeGenre;
use App\Entity\Company;
use App\Entity\Serie;
use App\Entity\SerieType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
            ->getResult();
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
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function findArtworkId(): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.artwork', 'a')
            ->andWhere('(a.language <> :eng AND a.language <> :fra) OR s.artwork IS NULL')
            ->setParameter('eng', 'eng')
            ->setParameter('fra', 'fra')
            ->andWhere('s.tvdbId IS NOT NULL')
            ->getQuery()
            ->getResult();
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
            ->getResult();
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
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function findAnimeWithoutScore(): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('t.name = :type')
            ->andWhere('s.score IS NULL')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function noFirstAired(): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.firstAired IS NULL')
            ->andWhere('s.tvdbId IS NOT NULL')
            ->andWhere('t.name <> :type OR s.tvdbId IN (302218)')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function noFirstAiredAnime(): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.firstAired IS NULL')
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->andWhere('s.tvdbId NOT IN (302218)')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function updateAired(): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.nextAired IS NULL OR s.nextAired < CURRENT_DATE()')
            ->andWhere('s.status <> :status OR s.status IS NULL OR s.status = :statusUpcoming')
            ->setParameter('status', "Ended")
            ->setParameter('statusUpcoming', "Upcoming")
            ->andWhere('s.tvdbId NOT IN (359149, 155291)')
            ->andWhere('t.name <> :type OR s.tvdbId IN (302218)')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function updateAiredAnime(): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.nextAired IS NULL OR s.nextAired < CURRENT_DATE()')
            ->andWhere('s.status <> :status OR s.status IS NULL')
            ->setParameter('status', "Ended")
            ->orWhere('s.status = :statusUpcoming')
            ->setParameter('statusUpcoming', "Upcoming")
            ->andWhere('s.tvdbId NOT IN (76703, 302218)')
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function ended(): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.status = :status')
            ->setParameter('status', "Ended")
            ->andWhere('s.tvdbId NOT IN (359149, 155291)')
            ->andWhere('t.name <> :type OR s.tvdbId IN (302218)')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function endedAnime(): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.status = :status')
            ->setParameter('status', "Ended")
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->andWhere('s.tvdbId NOT IN (76703, 302218)')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function endedAnimeAfterDate($date): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.status = :status')
            ->setParameter('status', "Ended")
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->andWhere('s.tvdbId NOT IN (76703, 302218)')
            ->andWhere('s.lastAired >= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function endedAnimeBeforeDate($date): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.status = :status')
            ->setParameter('status', "Ended")
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->andWhere('s.tvdbId NOT IN (76703, 302218)')
            ->andWhere('s.lastAired <= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function getAnimeWithoutLastDate(): array
    {

        $qb = $this->createQueryBuilder('s');

        return $qb
            ->leftJoin('s.serieType', 't')
            ->where('t.name = :typeName')
            ->andWhere('s.tvdbId NOT IN (302218)')
            ->andWhere(
                $qb->expr()->orX(
                    's.lastAired IS NULL',
                    $qb->expr()->andX(
                        's.lastAired < :currentDate',
                        's.status = :status'
                    )
                )
            )
            ->setParameter('typeName', 'Anime')
            ->setParameter('currentDate', new \DateTime())
            ->setParameter('status', 'Continuing')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function noThemeGenre(?string $text): array
    {

        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.animeGenres is EMPTY or s.animeThemes is EMPTY')
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime');

        if ($text) {
            $qb
                ->andWhere('s.name LIKE :text OR s.nameEng LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        return $qb
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function search(?SerieType $type, ?string $text): array
    {

        $qb = $this->createQueryBuilder('s');

        if ($type) {
            $qb
                ->andWhere('s.serieType = :type')
                ->setParameter('type', $type);
        }

        if ($text) {
            $qb
                ->andWhere('s.name LIKE :text OR s.nameEng LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        return $qb
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function serieByGenre(AnimeGenre $animeGenre): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.animeGenres IN (:idGenre)')
            ->setParameter('idGenre', $animeGenre)
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function noCompanies(): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.company is EMPTY')
            ->andWhere('s.tvdbId IS NOT NULL')
            ->andWhere('t.name <> :type')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function animeNoCompanies(): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('s.company is EMPTY')
            ->andWhere('s.tvdbId IS NOT NULL')
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function findAnimeWithLimit(int $limit): array
    {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.serieType', 't')
            ->andWhere('t.name = :type')
            ->setParameter('type', 'Anime')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Serie[] Returns an array of Serie objects
     */
    public function getSeriesByCompany(Company $company, ?string $text): array
    {

        $qb = $this->createQueryBuilder('s')
            ->leftjoin('s.company', 'c')
            ->andWhere('c = :company')
            ->setParameter('company', $company);

        if ($text) {
            $qb
                ->andWhere('s.name LIKE :text OR s.nameEng LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }

        return $qb
            ->getQuery()
            ->getResult();
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findLikeName($name)
    {

        return $this->createQueryBuilder('s')
            ->andWhere('s.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
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
