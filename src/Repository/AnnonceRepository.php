<?php
namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     * @return Annonce[] Returns an array of Annonce objects
     */
    public function findByDestinationAndDates(string $destination, string $arrival, string $departure): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.ville = :destination')
            ->andWhere('a.disponibilite >= :arrival')
            ->andWhere('a.disponibilite <= :departure')
            ->setParameter('destination', $destination)
            ->setParameter('arrival', $arrival)
            ->setParameter('departure', $departure)
            ->getQuery()
            ->getResult();
    }
}