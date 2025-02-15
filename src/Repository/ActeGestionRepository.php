<?php

namespace App\Repository;

use App\Entity\ActeGestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActeGestion>
 *
 * @method ActeGestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActeGestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActeGestion[]    findAll()
 * @method ActeGestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActeGestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActeGestion::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ActeGestion $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(ActeGestion $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * 
     */
    public function findByFilter(array $value)
    {
        if(!empty($value)){
            $sql = $this->createQueryBuilder('a');
            if(array_key_exists('dateDebut',$value) && array_key_exists('dateFin',$value)){
                $sql
                    ->andWhere('a.date_add BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $value['dateDebut'])
                    ->setParameter('dateFin', $value['dateFin']);
            }
            if(array_key_exists('gestionnaires',$value)){
                $sql
                    ->andWhere('a.gestionnaire = :gestionnaire')
                    ->setParameter('gestionnaire', $value['gestionnaires']);
            }
            if(array_key_exists('employeurs',$value)){
                $sql
                    ->andWhere('a.employeur = :employeurs')
                    ->setParameter('employeurs', $value['employeurs']);
            }
            if(array_key_exists('commercialisateurs',$value)){
                $sql
                    ->andWhere('a.commercialisateur = :commercialisateur')
                    ->setParameter('commercialisateur', $value['commercialisateurs']);
            }
            if(array_key_exists('departement',$value)){
                $sql
                    ->andWhere('a.departement = :departement')
                    ->setParameter('departement', $value['departement']);
            }
            if(array_key_exists('type',$value)){
                $sql
                    ->andWhere('a.type_population = :type_population')
                    ->setParameter('type_population', $value['type']);
            }
            return $sql
                ->orderBy('a.id', 'DESC')
                ->getQuery()
                ->getResult()
            ;
        }else{
            return null;
        }
    }

    // /**
    //  * @return ActeGestion[] Returns an array of ActeGestion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActeGestion
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
