<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Annonce;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/mes-reservation/list', name: 'reservation_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        
        // Si l'utilisateur est un propriétaire, on affiche les réservations de ses annonces
        if ($this->isGranted('ROLE_PROPRIETAIRE')) {
            $annonces = $em->getRepository(Annonce::class)->findBy(['id_utilisateur' => $user]);
            $reservations = [];
            foreach ($annonces as $annonce) {
                $reservations = array_merge($reservations, $annonce->getReservations()->toArray());
            }
        } else {
            // Sinon on affiche les réservations de l'utilisateur
            $reservations = $em->getRepository(Reservation::class)->findBy(['id_utilisateur' => $user]);
        }
        
        return $this->render('reservation/list.html.twig', [
            'reservations' => $reservations,
            'user' => $user
        ]);
    }

    #[Route('/mes-reservation/{id}', name: 'reservation_read')]
    public function read(EntityManagerInterface $em, int $id): Response
    {
        $reservation = $em->getRepository(Reservation::class)->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException("La réservation n'existe pas.");
        }
        
        // Vérification que l'utilisateur a le droit de voir cette réservation
        $user = $this->getUser();
        if ($user !== $reservation->getIdUtilisateur() && 
            $user !== $reservation->getIdAnnonce()->getIdUtilisateur()) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à cette réservation.");
        }
        
        return $this->render('reservation/read.html.twig', [
            'reservation' => $reservation
        ]);
    }

    #[Route('/mes-reservation/delete/{id}', name: 'reservation_delete')]
    public function delete(EntityManagerInterface $em, int $id): Response
    {
        $reservation = $em->getRepository(Reservation::class)->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException("La réservation n'existe pas.");
        }
        
        // Vérification que l'utilisateur a le droit de supprimer cette réservation
        $user = $this->getUser();
        if ($user !== $reservation->getIdUtilisateur() && 
            $user !== $reservation->getIdAnnonce()->getIdUtilisateur()) {
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de supprimer cette réservation.");
        }
        
        $em->remove($reservation);
        $em->flush();
        
        $this->addFlash('success', 'Réservation supprimée avec succès !');
        return $this->redirectToRoute('reservation_list');
    }
}