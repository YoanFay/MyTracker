<?php

namespace App\Repository;

use App\Entity\SerieUpdate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SerieUpdate>
 *
 * @method SerieUpdate|null find($id, $lockMode = null, $lockVersion = null)
 * @method SerieUpdate|null findOneBy(array $criteria, array $orderBy = null)
 * @method SerieUpdate[]    findAll()
 * @method SerieUpdate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieUpdateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SerieUpdate::class);
    }


    /**
     * @throws NonUniqueResultException
     */
    public function serieDate($serie, $date): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.serie = :serie')
            ->setParameter('serie', $serie)
            ->andWhere('s.updatedAt = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

//    /**
//     * @return SerieUpdate[] Returns an array of SerieUpdate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SerieUpdate
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
