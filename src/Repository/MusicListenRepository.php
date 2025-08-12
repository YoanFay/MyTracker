<?php

namespace App\Repository;

use App\Entity\MusicListen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MusicListen>
 *
 * @method MusicListen|null find($id, $lockMode = null, $lockVersion = null)
 * @method MusicListen|null findOneBy(array $criteria, array $orderBy = null)
 * @method MusicListen[]    findAll()
 * @method MusicListen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MusicListenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MusicListen::class);
    }

//    /**
//     * @return MusicListen[] Returns an array of MusicListen objects
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

//    public function findOneBySomeField($value): ?MusicListen
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
