<?php


// src/Controller/CompteController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    #[IsGranted('ROLE_USER')]
    public function index(UserInterface $user): Response // !user connecté ici
    {
        
        $annonces = $user->getAnnonces();
        $reservations = $user->getReservations();

        return $this->render('compte/index.html.twig', [
            'user' => $user,
            'annonces' => $annonces,
            'reservations' => $reservations,
        ]);
    }
}
