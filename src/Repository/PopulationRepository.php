<?php

namespace App\Repository;

use App\Entity\Population;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Population|null find($id, $lockMode = null, $lockVersion = null)
 * @method Population|null findOneBy(array $criteria, array $orderBy = null)
 * @method Population[]    findAll()
 * @method Population[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PopulationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Population::class);
    }

    public function deleteAll(): int
    {
        $qb = $this->createQueryBuilder('p');

        $qb->delete();

        return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }

    public function findOneByFilterData(array $data, string $nni = null, string $matricule = null, string $nomUsuel = null, string $nom = null, string $prenom = null)
    {
        $sql = $this->createQueryBuilder('p');
        if($data['departement'] != ''){
            $sql
            ->orWhere('p.departement = :departement')
            ->setParameter('departement', $data['departement']);
        }
        if($data['dernirEmployeurs'] != ''){
            $sql
            ->orWhere('p.dernier_employeur = :dernier_employeur')
            ->setParameter('dernier_employeur', $data['dernirEmployeurs']);
        }
        if($data['employeur'] != ''){
            $sql
            ->orWhere('p.employeur = :employeur')
            ->setParameter('employeur', $data['employeur']);
        }
        if($data['type'] != ''){
            $sql
            ->orWhere('p.type_population = :type_population')
            ->setParameter('type_population', $data['type']);
        }
        if($data['commercialisateur'] != ''){
            $sql
            ->orWhere('p.commercialisateur_pdl1 = :commercialisateur_pdl1')
            ->setParameter('commercialisateur_pdl1', $data['commercialisateur']);
        }
        if($data['commercialisateur'] != ''){
            $sql
            ->orWhere('p.commercialisateur_pdl2 = :commercialisateur_pdl2')
            ->setParameter('commercialisateur_pdl2', $data['commercialisateur']);
        }
        if($data['commercialisateur'] != ''){
            $sql
            ->orWhere('p.commercialisateur_pdl3 = :commercialisateur_pdl3')
            ->setParameter('commercialisateur_pdl3', $data['commercialisateur']);
        }
        if($nni){
            $sql
            ->andWhere('p.nni = :nni')
            ->setParameter('nni', $nni);
        }
        if($matricule){
            $sql
            ->andWhere('p.matricule = :matricule')
            ->setParameter('matricule', $matricule);
        }
        if($nom && $prenom && $nomUsuel){
            $sql
            ->andWhere('p.nom_usuel = :nom_usuel')
            ->andWhere('p.nom_naissance = :nom_naissance')
            ->andWhere('p.prenom = :prenom')
            ->setParameter('nom_usuel', $nomUsuel)
            ->setParameter('nom_naissance', $nom)
            ->setParameter('prenom', $prenom);
        }
        return $sql
        ->orderBy('p.id', 'ASC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }

    // /**
    //  * @return Population[] Returns an array of Population objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Population
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
