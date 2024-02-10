<?php

namespace App\Repository;

use App\Entity\MangaTome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MangaTome>
 *
 * @method MangaTome|null find($id, $lockMode = null, $lockVersion = null)
 * @method MangaTome|null findOneBy(array $criteria, array $orderBy = null)
 * @method MangaTome[]    findAll()
 * @method MangaTome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MangaTomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MangaTome::class);
    }

//    /**
//     * @return MangaTome[] Returns an array of MangaTome objects
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

//    public function findOneBySomeField($value): ?MangaTome
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
