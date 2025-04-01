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

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $destination = $request->query->get('destination');
        $arrival = $request->query->get('arrival');
        $departure = $request->query->get('departure');

        $annonces = $annonceRepository->findByDestinationAndDates($destination, $arrival, $departure);

        return $this->render('search/results.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[Route('/reservation/{id}', name: 'app_reservation')]
    public function reservation(int $id, Request $request, AnnonceRepository $annonceRepository, EntityManagerInterface $entityManager): Response
    {
        $annonce = $annonceRepository->find($id);

        if ($request->isMethod('POST')) {
            // Check if the user is logged in
            $user = $this->getUser();
            if (!$user) {
                // If the user is not logged in, redirect to the login page
                throw new AccessDeniedException('Vous devez être connecté pour effectuer une réservation.');
                // Or, alternatively, redirect to the login page:
                // return $this->redirectToRoute('app_login');
            }

            // Get the dates from the form
            $dateDebut = new \DateTime($request->request->get('date_debut'));
            $dateFin = new \DateTime($request->request->get('date_fin'));

            $reservation = new Reservation();
            $reservation->setDateDebut($dateDebut);
            $reservation->setDateFin($dateFin);
            $reservation->setIdAnnonce($annonce);
            $reservation->setIdUtilisateur($user);

            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réservation a été effectuée avec succès !');

            return $this->redirectToRoute('app_home'); // Redirect to a confirmation page or home
        }

        return $this->render('reservation/form.html.twig', [
            'annonce' => $annonce,
        ]);
    }
}