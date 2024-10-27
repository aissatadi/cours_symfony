<?php

// src/Repository/ClientRepository.php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findByFilters(?string $surname, ?string $telephone): array
    {
        $qb = $this->createQueryBuilder('c');
    
        if ($surname) {
            $qb->andWhere('c.surname LIKE :surname')
               ->setParameter('surname', '%' . $surname . '%');
        }
    
        if ($telephone) {
            $qb->andWhere('c.telephone LIKE :telephone')
               ->setParameter('telephone', '%' . $telephone . '%');
        }
    
        return $qb->getQuery()->getResult();
    }
    





    //    /**
    //     * @return Client[] Returns an array of Client objects
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

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
