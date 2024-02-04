<?php

namespace App\Repository;

use App\Entity\TagsType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TagsType>
 *
 * @method TagsType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagsType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagsType[]    findAll()
 * @method TagsType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagsTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagsType::class);
    }

//    /**
//     * @return TagsType[] Returns an array of TagsType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TagsType
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
