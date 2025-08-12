<?php

namespace App\Repository;

use App\Entity\MusicArtist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MusicArtist>
 *
 * @method MusicArtist|null find($id, $lockMode = null, $lockVersion = null)
 * @method MusicArtist|null findOneBy(array $criteria, array $orderBy = null)
 * @method MusicArtist[]    findAll()
 * @method MusicArtist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MusicArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MusicArtist::class);
    }

//    /**
//     * @return MusicArtist[] Returns an array of MusicArtist objects
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

//    public function findOneBySomeField($value): ?MusicArtist
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
