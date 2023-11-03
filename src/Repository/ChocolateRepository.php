<?php

namespace App\Repository;

use App\Entity\Chocolate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chocolate>
 *
 * @method Chocolate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chocolate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chocolate[]    findAll()
 * @method Chocolate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChocolateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chocolate::class);
    }

//    /**
//     * @return Chocolate[] Returns an array of Chocolate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Chocolate
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
