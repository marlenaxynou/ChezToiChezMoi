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
            
            // Gestion des images
            $images = $form->get('images')->getData();
            foreach ($images as $imageFile) {
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
            
                    
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
            
                    // Créer une nouvelle entité Image
                    $image = new Image();
                    $image->setChemin((string) $newFilename);
                    $image->setIdAnnonce($annonce);
                    $em->persist($image);
                }
            }
            

            
            $this->addFlash('success', 'Votre annonce a été créée avec succès !');
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('annonce_create'); // Reste sur la même page
        }

        return $this->render('annonce/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
