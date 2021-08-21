<?php

namespace App\Repository;

use App\Entity\Panier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Panier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Panier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Panier[]    findAll()
 * @method Panier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panier::class);
    }

    public function findPanierUser($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.utilisateur = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPanierUserAchete($value)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.dateAchat')
            ->where('p.etat = :etat')
            ->setParameter('etat', 1)
            ->andWhere('p.utilisateur = :user')
            ->setParameter('user', $value)
            ->getQuery()
            ->getResult() 
        ;
    }


    public function findPanierNonAchete()
    {
        return $this->createQueryBuilder('p')
            ->select('u.email','p.id' , 'c')
            ->leftJoin('App\Entity\ContenuPanier',
                        'c',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                       'p.id = c.panier')
            ->leftJoin('App\Entity\Produit',
                        'pr',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'pr.id = c.produit')
            ->leftJoin('App\Entity\User',
                        'u',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'u.id = p.utilisateur')
            ->where('p.etat = :etat')
            ->setParameter('etat', 0)
            ->getQuery()
            ->getResult() 
        ;
    }

    // /**
    //  * @return Panier[] Returns an array of Panier objects
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
    public function findOneBySomeField($value): ?Panier
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
