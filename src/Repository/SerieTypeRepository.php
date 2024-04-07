<?php

namespace App\Repository;

use App\Entity\SerieType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SerieType>
 *
 * @method SerieType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SerieType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SerieType[]    findAll()
 * @method SerieType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SerieType::class);
    }

//    /**
//     * @return SerieType[] Returns an array of SerieType objects
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

//    public function findOneBySomeField($value): ?SerieType
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
