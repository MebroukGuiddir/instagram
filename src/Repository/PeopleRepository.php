<?php

namespace App\Repository;

use App\Entity\People;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method People|null find($id, $lockMode = null, $lockVersion = null)
 * @method People|null findOneBy(array $criteria, array $orderBy = null)
 * @method People[]    findAll()
 * @method People[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeopleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, People::class);
    }

    // /**
    //  * @return People[] Returns an array of People objects
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
   
    public function findAllByAccount($account)
    {  
        return $this->createQueryBuilder('p')
            ->andWhere('p.account = :acc')
            ->setParameter('acc', $account)
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findOneByUsername($username,$account): ?People
    {   
        return $this->createQueryBuilder('p')
            ->andWhere('p.username = :uesr') 
            ->andWhere('p.account = :acc')
            ->setParameter('user', $username) 
            ->setParameter('acc', $account)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function findOneByInstaId($instaID,$account): ?People
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.instaId = :val')
            ->andWhere('p.account = :acc')
            ->setParameter('val', $instaID)
            ->setParameter('acc', $account)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
