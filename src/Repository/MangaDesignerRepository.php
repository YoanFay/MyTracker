<?php

namespace App\Repository;

use App\Entity\MangaAuthor;
use App\Entity\MangaDesigner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MangaDesigner>
 *
 * @method MangaAuthor|null find($id, $lockMode = null, $lockVersion = null)
 * @method MangaAuthor|null findOneBy(array $criteria, array $orderBy = null)
 * @method MangaAuthor[]    findAll()
 * @method MangaAuthor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MangaDesignerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MangaDesigner::class);
    }

//    /**
//     * @return MangaAuthor[] Returns an array of MangaAuthor objects
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

//    public function findOneBySomeField($value): ?MangaAuthor
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
