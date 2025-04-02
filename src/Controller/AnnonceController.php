<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Image;
use App\Form\AnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AnnonceController extends AbstractController
{
    #[Route('/annonce/create', name: 'annonce_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setDateCreation(new \DateTime());
            
            $images = $form->get('images')->getData();
            foreach ($images as $imageFile) {
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                    
                    $imageFile->move($this->getParameter('images_directory'), $newFilename);
                    
                    $image = new Image();
                    $image->setChemin((string) $newFilename);
                    $image->setIdAnnonce($annonce);
                    $em->persist($image);
                }
            }
            
            $user = $this->getUser();
            if (!$user) {
                throw new \Exception("Aucun utilisateur connecté !");
            }
            
            $annonce->setIdUtilisateur($user);
            $this->addFlash('success', 'Votre annonce a été créée avec succès !');
            $em->persist($annonce);
            $em->flush();
            
            return $this->redirectToRoute('annonce_create');
        }
        
        return $this->render('annonce/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/annonce/list', name: 'annonce_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $annonces = $em->getRepository(Annonce::class)->findAll();
        
        return $this->render('annonce/list.html.twig', [
            'annonces' => $annonces,
            'user' => $this->getUser()
        ]);
    }

    #[Route('/annonce/{id}', name: 'annonce_read')]
    public function read(EntityManagerInterface $em, int $id): Response
    {
        $annonce = $em->getRepository(Annonce::class)->find($id);
        if (!$annonce) {
            throw $this->createNotFoundException("L'annonce n'existe pas.");
        }
        
        return $this->render('annonce/read.html.twig', ['annonce' => $annonce]);
    }

    #[Route('/annonce/edit/{id}', name: 'annonce_edit')]
    public function update(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $annonce = $em->getRepository(Annonce::class)->find($id);
        if (!$annonce) {
            throw $this->createNotFoundException("L'annonce n'existe pas.");
        }
        
        if ($this->getUser() !== $annonce->getIdUtilisateur()) {
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de modifier cette annonce.");
        }
        
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Annonce mise à jour avec succès !');
            return $this->redirectToRoute('annonce_list');
        }
        
        return $this->render('annonce/update.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/annonce/delete/{id}', name: 'annonce_delete')]
    public function delete(EntityManagerInterface $em, int $id): Response
    {
        $annonce = $em->getRepository(Annonce::class)->find($id);
        if (!$annonce) {
            throw $this->createNotFoundException("L'annonce n'existe pas.");
        }
        
        if ($this->getUser() !== $annonce->getIdUtilisateur()) {
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de supprimer cette annonce.");
        }
        
        foreach ($annonce->getImages() as $image) {
            $annonce->removeImage($image);
            $em->remove($image);
        }
    
        $em->remove($annonce);
        $em->flush();
        
        $this->addFlash('success', 'Annonce supprimée avec succès !');
        return $this->redirectToRoute('annonce_list');
    }
    
}
