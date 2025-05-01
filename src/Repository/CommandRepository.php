<?php

namespace App\Repository;

use App\Entity\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Command>
 */
class CommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }

    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.idUser = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('c.dateCommand', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStatutCom(bool $statut): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.statut_com = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('c.dateCommand', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
