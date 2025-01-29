# ChezToiChezMoi
1)Description du projet

  Ce projet vise à la création d’un site web de réservation de logements entre particuliers. L’objectif est de permettre à des propriétaires de publier des annonces pour leurs biens et aux utilisateurs de rechercher, réserver, et évaluer ces logements. ChezToiChezMoi ambitionne de proposer une plateforme intuitive et efficace pour faciliter les échanges entre      propriétaires et voyageurs.

  
  Ce projet a été développé en collaboration avec Alexandre , Eleni , Manal, et Henry, chacun ayant contribué à différentes étapes, de la conception à la réalisation.

2)Fonctionnalités du projet
  
  -Gestion des utilisateurs
        Permet aux utilisateurs de s'inscrire, de se connecter et de gérer leurs profils.
  
  -Gestion des réservations
        Offre aux clients la possibilité de réserver un logement en fonction des disponibilités.

  -Recherche et filtrage
        Permet de rechercher des logements et de les filtrer selon différents critères (prix, localisation, type de logement, etc.).

  -Gestion des annonces pour les propriétaires
        Donne la possibilité aux propriétaires de créer, modifier et supprimer leurs annonces.

  -Avis et notes
        Inclut un lien sous chaque annonce permettant aux utilisateurs de consulter les avis et notes pour les aider à prendre une décision.
  
  

<img width="272" alt="Screenshot 2025-01-29 at 3 13 27 PM" src="https://github.com/user-attachments/assets/d315cd8e-3172-4e11-9a9d-44a47305e76c" />


Table : Utilisateur
idUtilisateur : Identifiant unique (clé primaire)
nom : Nom de l’utilisateur
prenom : Prénom de l’utilisateur
email : Adresse e-mail 
motDePasse : Mot de passe sécurisé (haché)
dateInscription : Date d’inscription


Table : Annonce
idAnnonce : Identifiant unique (clé primaire) (int)
titre : Titre de l’annonce (String)
description : Description détaillée de l’annonce (String)
prix : Prix par nuit (int)
adresse : Adresse du logement (String)
ville : Ville où se trouve le logement (String)
nbPersonne : Capacité d’accueil (nombre de personnes) (int)
idUtilisateur : Référence à l’utilisateur propriétaire (clé étrangère vers idUtilisateur) (int)
dateCreation : Date de création de l’annonce (DateTime)
disponibilite : Disponibilités (à partir de quand l’annonce est disponible) (DateTime)


Table : Réservation
idReservation : Identifiant unique (clé primaire) (int)
idAnnonce : Référence à l’annonce réservée (clé étrangère vers idAnnonce) (int)
idClient : Référence au client ayant réservé (clé étrangère vers idUtilisateur) (int)
dateDebut : Date de début de la réservation (DateTime)
dateFin : Date de fin de la réservation  (DateTime)


Table : Avis
idAvis : Identifiant unique (clé primaire) (int)
idAnnonce : Référence à l’annonce concernée (clé étrangère vers idAnnonce) (int)
idClient : Référence au client ayant laissé l’avis (clé étrangère vers idUtilisateur) (int)
note : Note donnée (de 1 à 5) (int)
commentaire : Commentaire de l’utilisateur (String)
dateAvis : Date de publication de l’avis (DateTime)
