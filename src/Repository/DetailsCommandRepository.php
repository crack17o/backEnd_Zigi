<?php

namespace App\Repository;

use App\Entity\DetailsCommand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsCommand>
 */
class DetailsCommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsCommand::class);
    }

    public function findByCommandId(int $commandId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.id_Command = :commandId')
            ->setParameter('commandId', $commandId)
            ->getQuery()
            ->getResult();
    }
}
