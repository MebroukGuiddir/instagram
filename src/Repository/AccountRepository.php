<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Account::class);
    }

    // /**
    //  * @return Account[] Returns an array of Account objects
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


    public function findOneBySomeField($value): ?Account
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.username = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
     
    public function  findOneByUsername($username): ?Account
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.username = :val')
            ->setParameter('val', $username)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


   

    /**
     * @method assign instagram instance to user or create it if not exist
     */
    public function selectAccount($username){
        //check if account exist
        return $this->createQueryBuilder('a')
            ->andWhere('a.username = ?1')
            ->setParameter('1', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
