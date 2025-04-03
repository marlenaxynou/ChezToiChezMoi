<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Repository\UtilisateurRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\SendEmailService;
use App\Service\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, JWTService $jwt, SendEmailService $mail): Response
    {
        if ($this->getUser()) {
            // Si l'utilisateur est déjà connecté, le rediriger vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setMotDePasse($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            //Géneration du Token
        
            //Header
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];

            //Playload
            $payload = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ];

            //On génère le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwt_secret'));

            //dd($token);

            //Envoi du mail
            $mail->send(
                'no-reply@cheztoichezmoi.com',
                $user->getEmail(),
                'Activation de votre compte chez ChezToiChezMoi',
                'register.html.twig',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );
            
            $this->addFlash('success', 'Votre compte a bien été créé, un email vous a été envoyé pour activer votre compte');

            return $security->login($user, LoginFormAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verif/{token}', name: 'app_verify_user')]
    public function verifUser(
        $token, 
        JWTService $jwt, 
        UtilisateurRepository $utilisateurRepository, 
        EntityManagerInterface $entityManager
    ): Response {
        if(!$jwt->isValid($token) || $jwt->isExpired($token) || !$jwt->check($token, $this->getParameter('app.jwt_secret'))) {
            $this->addFlash('danger', 'Le lien d\'activation est invalide ou a expiré');
            return $this->redirectToRoute('app_login');
        }
    
        $payload = $jwt->getPayload($token);
        $user = $utilisateurRepository->find($payload['id']);
    
        if(!$user) {
            $this->addFlash('danger', 'Utilisateur non trouvé');
            return $this->redirectToRoute('app_register');
        }
    
        if($user->isVerified()) {
            $this->addFlash('warning', 'Votre compte est déjà activé');
            return $this->redirectToRoute('app_login');
        }

        $user->setVerified(true);
        $entityManager->flush();
    
        $this->addFlash('success', 'Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_login');
    }
}