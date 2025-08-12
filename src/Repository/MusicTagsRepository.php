<?php

namespace App\Repository;

use App\Entity\MusicTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MusicTags>
 *
 * @method MusicTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method MusicTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method MusicTags[]    findAll()
 * @method MusicTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MusicTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MusicTags::class);
    }

//    /**
//     * @return MusicTags[] Returns an array of MusicTags objects
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

//    public function findOneBySomeField($value): ?MusicTags
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
