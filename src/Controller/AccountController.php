<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        $annonces = $user->getAnnonces();
        $reservations = $user->getReservations();

        return $this->render('compte/index.html.twig', [
            'user' => $user,
            'annonces' => $annonces,
            'reservations' => $reservations,
        ]);
    }
}
