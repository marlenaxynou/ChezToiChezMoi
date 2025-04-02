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

    public function findAvailableAnnonces(string $destination, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin, int $persons): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.ville = :destination')
            ->setParameter('destination', $destination)
            ->andWhere('a.nb_personne >= :persons')
            ->setParameter('persons', $persons)
            ->leftJoin('a.reservations', 'res')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('res.id'), // No reservations
                    $qb->expr()->not(
                        $qb->expr()->andX(
                            $qb->expr()->lte('res.date_debut', ':dateFin'),
                            $qb->expr()->gte('res.date_fin', ':dateDebut')
                        )
                    )
                )
            )
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin);

        return $qb->getQuery()->getResult();
    }
}