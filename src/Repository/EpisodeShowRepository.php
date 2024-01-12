<?php

namespace App\Repository;

use App\Entity\EpisodeShow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EpisodeShow>
 *
 * @method EpisodeShow|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpisodeShow|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpisodeShow[]    findAll()
 * @method EpisodeShow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeShowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpisodeShow::class);
    }

    public function add(EpisodeShow $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EpisodeShow $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function findElementsByDay()
    {
        return $this->createQueryBuilder('e')
            ->select("DATE_FORMAT(e.showDate, '%Y-%m-%d') as day, e as episodes")
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return EpisodeShow[] Returns an array of EpisodeShow objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EpisodeShow
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
