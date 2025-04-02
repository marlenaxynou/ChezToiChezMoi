<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\ReservationRepository;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $destination = $request->query->get('destination');
        $arrival = $request->query->get('arrival');
        $departure = $request->query->get('departure');

        if (empty($destination)) {
            $this->addFlash('error', 'Destination is required.');
            return $this->redirectToRoute('app_home');
        }

        $dateDebut = new \DateTime($arrival);
        $dateFin = new \DateTime($departure);

        $annonces = $annonceRepository->findAvailableAnnonces($destination, $dateDebut, $dateFin);

        return $this->render('search/results.html.twig', [
            'annonces' => $annonces,
            'arrival' => $arrival,
            'departure' => $departure,
            'destination' => $destination,
        ]);
    }

    #[Route('/reservation/{id}', name: 'app_reservation')]
    public function reservation(int $id, Request $request, AnnonceRepository $annonceRepository, SessionInterface $session): Response
    {
        $annonce = $annonceRepository->find($id);
        // Get arrival and departure dates from the request query parameters
        $arrival = $request->query->get('date_debut');
        $departure = $request->query->get('date_fin');

        try {
            $arrivalDate = new \DateTime($arrival);
            $departureDate = new \DateTime($departure);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Invalid date format. Please use YYYY-MM-DD.');
            return $this->redirectToRoute('app_home');
        }

        // Format DateTime objects as strings for the hidden input fields
        $arrivalString = $arrivalDate->format('Y-m-d');
        $departureString = $departureDate->format('Y-m-d');

        // Store reservation details in session
        $reservationData = [
            'annonce_id' => $id,
            'date_debut' => $arrivalString,
            'date_fin' => $departureString,
            'destination' => $request->query->get('destination'),
        ];
        $session->set('reservation_data', $reservationData);

        dump($session->get('reservation_data')); // Add this line - Check data being stored

        return $this->render('reservation/form.html.twig', [
            'annonce' => $annonce,
            'arrival' => $arrivalDate,
            'departure' => $departureDate,
            'arrivalString' => $arrivalString, // Pass the formatted arrival date
            'departureString' => $departureString, // Pass the formatted departure date
        ]);
    }

    #[Route('/reservation_submit', name: 'app_reservation_submit')]
    public function reservationSubmit(Request $request, AnnonceRepository $annonceRepository, EntityManagerInterface $entityManager, ?UserInterface $user, SessionInterface $session, ReservationRepository $reservationRepository): Response
    {
        // Debug: Check if the user is authenticated
        if (!$user) {
            dump('User is not authenticated!'); // Add this line
            throw new AccessDeniedException('Vous devez être connecté pour effectuer une réservation.');
        }

        // Debug: Check if reservation data exists in the session
        $reservationData = $session->get('reservation_data');
        dump($reservationData); // Add this line - Check data before reservation

        if (!$reservationData) {
            $this->addFlash('error', 'Session expirée, veuillez réessayer.');
            return $this->redirectToRoute('app_home'); // Add this line - Redirect if no session data
        }

        $session->remove('reservation_data'); // Remove the data after retrieving it

        $annonce = $annonceRepository->find($reservationData['annonce_id']);

        try {
            $dateDebut = new \DateTime($reservationData['date_debut']);
            $dateFin = new \DateTime($reservationData['date_fin']);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Invalid date format in session. Please try again.');
            return $this->redirectToRoute('app_home');
        }

        // Check for existing reservations
        $existingReservation = $reservationRepository->findExistingReservation($annonce, $dateDebut, $dateFin);

        if ($existingReservation) {
            $this->addFlash('error', 'Ces dates sont déjà réservées. Veuillez choisir d\'autres dates.');
            return $this->redirectToRoute('app_home'); // Or back to the reservation form
        }

        $reservation = new Reservation();
        $reservation->setDateDebut($dateDebut);
        $reservation->setDateFin($dateFin);
        $reservation->setIdAnnonce($annonce);
        $reservation->setIdUtilisateur($user);

        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été effectuée avec succès !');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/login_success', name: 'app_login_success')]
    public function loginSuccess(SessionInterface $session): Response
    {
        dump($session->get('reservation_data')); // Add this line - Check data being retrieved

        if ($session->has('reservation_data')) {
             return $this->redirectToRoute('app_reservation_submit');
        }

        return $this->redirectToRoute('app_home');
    }
}