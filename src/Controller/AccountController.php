<?php

namespace App\Controller;

use App\Form\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;


class AccountController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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

    #[Route('/compte/edit', name: 'app_compte_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour.');

            return $this->redirectToRoute('app_compte');
        }

        return $this->render('compte/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}