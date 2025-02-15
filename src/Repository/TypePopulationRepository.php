<?php

namespace App\Repository;

use App\Entity\TypePopulation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypePopulation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypePopulation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypePopulation[]    findAll()
 * @method TypePopulation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypePopulationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypePopulation::class);
    }

    // /**
    //  * @return TypePopulation[] Returns an array of TypePopulation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypePopulation
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
