<?php

namespace App\Repository;

use App\Entity\ContenuPanier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContenuPanier|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContenuPanier|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContenuPanier[]    findAll()
 * @method ContenuPanier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContenuPanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContenuPanier::class);
    }

    public function findCommandeUser($value)
    {
        return $this->createQueryBuilder('c')
            ->select('p.id','p.dateAchat','pr.prix','c.quantite')
            ->leftJoin('App\Entity\Panier',
                        'p',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                       'p.id = c.panier')
            ->leftJoin('App\Entity\Produit',
                        'pr',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'pr.id = c.produit')
            ->where('p.etat = :etat')
            ->setParameter('etat', 1)
            ->andWhere('p.utilisateur = :user')
            ->setParameter('user', $value)
            ->getQuery()
            ->getResult() 
        ;
    }

    public function findCommandeDetailUser($value, $value2)
    {
        return $this->createQueryBuilder('c')
            ->select('pr.nom','c.quantite','pr.prix','p.dateAchat')
            ->leftJoin('App\Entity\Panier',
                        'p',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                       'p.id = c.panier')
            ->leftJoin('App\Entity\Produit',
                        'pr',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'pr.id = c.produit')
            ->where('p.etat = :etat')
            ->setParameter('etat', 1)
            ->andWhere('p.utilisateur = :user')
            ->setParameter('user', $value)
            ->andWhere('p.id = :panier')
            ->setParameter('panier', $value2)
            ->getQuery()
            ->getResult() 
        ;
    }

    // /**
    //  * @return ContenuPanier[] Returns an array of ContenuPanier objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContenuPanier
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
