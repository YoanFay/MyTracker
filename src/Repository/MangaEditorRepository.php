<?php

namespace App\Repository;

use App\Entity\MangaEditor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MangaEditor>
 *
 * @method MangaEditor|null find($id, $lockMode = null, $lockVersion = null)
 * @method MangaEditor|null findOneBy(array $criteria, array $orderBy = null)
 * @method MangaEditor[]    findAll()
 * @method MangaEditor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MangaEditorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MangaEditor::class);
    }

//    /**
//     * @return MangaEditor[] Returns an array of MangaEditor objects
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

//    public function findOneBySomeField($value): ?MangaEditor
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
