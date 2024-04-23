<?php

namespace App\Repository;

use App\Entity\Apart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Apart>
 *
 * @method Apart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apart[]    findAll()
 * @method Apart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apart::class);
    }

//    /**
//     * @return Apart[] Returns an array of Apart objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Apart
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
