<?php
// src/Repository/AnnonceRepository.php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }


    
    public function findAvailableAnnonces(string $destination, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.ville = :destination')
        ->setParameter('destination', $destination)
        ->leftJoin('a.reservations', 'r')
        ->andWhere(
            $qb->expr()->orX(
                $qb->expr()->isNull('r.id'), // No reservations exist
                $qb->expr()->not(
                    $qb->expr()->andX(
                        $qb->expr()->lt('r.date_fin', ':dateDebut'), // Existing reservation ends before the new start date
                        $qb->expr()->gt('r.date_debut', ':dateFin')  // Existing reservation starts after the new end date
                    )
                )
            )
        )
        ->setParameter('dateDebut', $dateDebut)
        ->setParameter('dateFin', $dateFin);

        return $qb->getQuery()->getResult();
    }

}