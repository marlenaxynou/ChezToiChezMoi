<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria = null, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    public function save(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAvailableAnnonces(string $destination, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin, int $persons): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.ville = :destination')
            ->setParameter('destination', $destination)
            ->andWhere('a.nb_personne >= :persons')
            ->setParameter('persons', $persons)
            ->andWhere(
                $qb->expr()->not(
                    $qb->expr()->exists(
                        'SELECT r.id FROM App\Entity\Reservation r
                        WHERE r.id_annonce = a.id
                        AND (
                            (r.date_debut <= :dateFin AND r.date_fin >= :dateDebut)
                        )'
                    )
                )
            )
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin);

        return $qb->getQuery()->getResult();
    }

    public function findAvailableAnnoncesForChambres(?\DateTimeInterface $dateDebut, ?\DateTimeInterface $dateFin, ?int $nbPersonnes): array
    {
        $qb = $this->createQueryBuilder('a');

        if ($dateDebut && $dateFin) {
            $qb->andWhere(
                $qb->expr()->not(
                    $qb->expr()->exists(
                        'SELECT r.id FROM App\Entity\Reservation r
                        WHERE r.id_annonce = a.id
                        AND (
                            (r.date_debut <= :dateFin AND r.date_fin >= :dateDebut)
                        )'
                    )
                )
            )
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin);
        }

        if ($nbPersonnes) {
            $qb->andWhere('a.nb_personne >= :nbPersonnes')
                ->setParameter('nbPersonnes', $nbPersonnes);
        }

        return $qb->getQuery()->getResult();
    }
}