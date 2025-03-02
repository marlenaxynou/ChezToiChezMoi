<?php
namespace App\Controller;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function reservation(int $id, Request $request, AnnonceRepository $annonceRepository): Response
    {
        $annonce = $annonceRepository->find($id);

        if ($request->isMethod('POST')) {
            // Gérer la réservation ici
        }

        return $this->render('reservation/form.html.twig', [
            'annonce' => $annonce,
        ]);
    }
}