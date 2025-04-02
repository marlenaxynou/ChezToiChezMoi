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
            $persons = $request->query->get('persons', 1); // Default to 1 person if not provided
    
            if (empty($destination)) {
                $this->addFlash('error', 'Destination is required.');
                return $this->redirectToRoute('app_home');
            }
    
            $dateDebut = new \DateTime($arrival);
            $dateFin = new \DateTime($departure);
    
            $annonces = $annonceRepository->findAvailableAnnonces($destination, $dateDebut, $dateFin, $persons);
    
            return $this->render('search/results.html.twig', [
                'annonces' => $annonces,
                'arrival' => $arrival,
                'departure' => $departure,
                'destination' => $destination,
                'persons' => $persons, // Pass the number of persons to the template
            ]);
        }



   
   
        #[Route('/reservation/{id}', name: 'app_reservation')]
        public function reservation(int $id, Request $request, AnnonceRepository $annonceRepository, SessionInterface $session, ?UserInterface $user): Response
        {
            // Check if the user is logged in
            if (!$user) {
                // Store reservation data in session
                $reservationData = [
                    'annonce_id' => $id,
                    'date_debut' => $request->query->get('date_debut'),
                    'date_fin' => $request->query->get('date_fin'),
                    'persons' => $request->query->get('persons', 1),
                    'destination' => $request->query->get('destination'),
                ];
                $session->set('reservation_data', $reservationData);
    
                // Redirect to login page
                return $this->redirectToRoute('app_login');
            }
    
            $annonce = $annonceRepository->find($id);
            // Get arrival and departure dates from the request query parameters
            $arrival = $request->query->get('date_debut');
            $departure = $request->query->get('date_fin');

            if (!$arrival || !$departure) {
                $this->addFlash('error', 'Veuillez spécifier les dates d\'arrivée et de départ.');
                return $this->redirectToRoute('app_home');
            }
        
            $persons = $request->query->get('persons', 1); // Default to 1 person if not provided
        
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
                'persons' => $persons, // Store the number of persons in the session
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
    #[IsGranted('ROLE_USER')]
    public function reservationSubmit(Request $request, AnnonceRepository $annonceRepository, EntityManagerInterface $entityManager, ?UserInterface $user, SessionInterface $session, ReservationRepository $reservationRepository): Response
   
    {
        $annonceId = $request->request->get('annonce_id');
        $dateDebutString = $request->request->get('date_debut');
        $dateFinString = $request->request->get('date_fin');
        $persons = $session->get('reservation_data')['persons'];

        $annonce = $annonceRepository->find($annonceId);

        if (!$annonce) {
            $this->addFlash('error', 'Annonce introuvable.');
            return $this->redirectToRoute('app_home');
        }

        try {
            $dateDebut = new \DateTime($dateDebutString);
            $dateFin = new \DateTime($dateFinString);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Format de date invalide.');
            return $this->redirectToRoute('app_home');
        }
        $existingReservation = $reservationRepository->findExistingReservation($annonce, $dateDebut, $dateFin);
        if ($existingReservation) {
            $this->addFlash('error', 'Ces dates sont déjà réservées. Veuillez choisir d\'autres dates.');
            return $this->redirectToRoute('app_home');
        }

        if ($persons > $annonce->getNbPersonne()) {
            $this->addFlash('error', 'La capacité dhébergement ne peut pas accueillir autant de personnes.');
            return $this->redirectToRoute('app_home');
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

    #[Route('/chambres', name: 'app_chambres')]
public function chambres(Request $request, AnnonceRepository $annonceRepository): Response
{
    $dateDebutString = $request->query->get('date_debut');
    $dateFinString = $request->query->get('date_fin');

    $dateDebut = null;
    $dateFin = null;

    if ($dateDebutString && $dateFinString) {
        try {
            $dateDebut = new \DateTime($dateDebutString);
            $dateFin = new \DateTime($dateFinString);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Invalid date format.');
            return $this->redirectToRoute('app_chambres');
        }
    }

    $annonces = $annonceRepository->findAvailableAnnoncesForChambres($dateDebut, $dateFin);

    return $this->render('search/chambres.html.twig', [
        'annonces' => $annonces,
        'date_debut' => $dateDebutString,
        'date_fin' => $dateFinString,
    ]);
}
}