<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findExistingReservation(Annonce $annonce, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): ?Reservation
    {
        $qb = $this->createQueryBuilder('r');

        $qb->where('r.id_annonce = :annonce')
            ->setParameter('annonce', $annonce)
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->lte('r.date_debut', ':dateFin'),
                        $qb->expr()->gte('r.date_fin', ':dateDebut')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->lte('r.date_fin', ':dateFin'),
                        $qb->expr()->gte('r.date_debut', ':dateDebut')
                    )
                )
            )
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin);

        $results = $qb->getQuery()->getResult();

        if (count($results) > 0) {
            return $results[0]; // Return the first result
        }

        return null; // No overlapping reservation found
    }
}