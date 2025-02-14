# ChezToiChezMoi <img src="https://cdn.discordapp.com/attachments/1147986579881721877/1332869760626589696/ChezToiChezMoi.png?ex=67b08872&is=67af36f2&hm=9f62f5e31b0ba8bc583671ce92b8c7a36ad6191b445f239b277ecbcca796563d&" alt="Logo ChezToiChezMoi" width="50"/>


[![Symfony](https://img.shields.io/badge/Symfony-7.x-green)](https://symfony.com/)  
[![PHP](https://img.shields.io/badge/PHP-8.3-blue)](https://www.php.net/)  
[![Docker](https://img.shields.io/badge/Docker-Compose-blue)](https://www.docker.com/)

## Description
ChezToiChezMoi est une plateforme de réservation de logements entre particuliers. Les propriétaires peuvent publier des annonces pour leurs biens, tandis que les utilisateurs peuvent rechercher, réserver et évaluer les logements. L’objectif est de proposer une expérience intuitive et fluide pour simplifier les échanges entre propriétaires et voyageurs.

Projet développé en collaboration avec **Alexandre, Eleni, Manal et Henry**.

## Fonctionnalités principales

- **Gestion des utilisateurs** : Inscription, connexion et gestion du profil.
- **Gestion des réservations** : Réservation de logements selon leur disponibilité.
- **Recherche et filtrage** : Recherche avancée avec filtres (prix, localisation, type de logement, etc.).
- **Gestion des annonces** : Création, modification et suppression d’annonces par les propriétaires.
- **Avis et notes** : Consultation et publication d’avis et de notes sur les logements.

![Aperçu du projet](https://github.com/user-attachments/assets/d315cd8e-3172-4e11-9a9d-44a47305e76c)

## Modèle de base de données

### **Table : Utilisateur**
| Champ            | Type        | Description                          |
|-----------------|------------|--------------------------------------|
| idUtilisateur   | INT (PK)    | Identifiant unique                  |
| nom            | STRING      | Nom de l’utilisateur                |
| prenom         | STRING      | Prénom de l’utilisateur             |
| email          | STRING      | Adresse e-mail unique               |
| motDePasse     | STRING (haché) | Mot de passe sécurisé              |
| dateInscription| DATETIME    | Date d’inscription                  |

### **Table : Annonce**
| Champ         | Type        | Description                                |
|--------------|------------|--------------------------------------------|
| idAnnonce    | INT (PK)    | Identifiant unique                         |
| titre        | STRING      | Titre de l’annonce                         |
| description  | STRING      | Description détaillée                      |
| prix         | INT         | Prix par nuit                              |
| adresse     | STRING      | Adresse du logement                        |
| ville       | STRING      | Ville où se trouve le logement             |
| nbPersonne  | INT         | Capacité d’accueil                         |
| idUtilisateur | INT (FK)   | Référence au propriétaire                  |
| dateCreation | DATETIME    | Date de création de l’annonce              |
| disponibilite | DATETIME    | Disponibilité du logement                  |

### **Table : Réservation**
| Champ        | Type        | Description                                |
|-------------|------------|--------------------------------------------|
| idReservation | INT (PK)   | Identifiant unique                        |
| idAnnonce    | INT (FK)   | Référence à l’annonce réservée            |
| idUtilisateur | INT (FK)   | Référence au client                       |
| dateDebut    | DATETIME   | Date de début de la réservation           |
| dateFin      | DATETIME   | Date de fin de la réservation             |

### **Table : Avis**
| Champ        | Type        | Description                                |
|-------------|------------|--------------------------------------------|
| idAvis      | INT (PK)   | Identifiant unique                        |
| idAnnonce   | INT (FK)   | Référence à l’annonce concernée           |
| idUtilisateur | INT (FK)   | Référence au client ayant laissé un avis  |
| note        | INT (1-5)  | Note attribuée                            |
| commentaire | STRING     | Commentaire laissé                        |
| dateAvis    | DATETIME   | Date de publication de l’avis             |

---

💡 **Technologies utilisées** : Symfony 7.x, PHP 8.3, Docker Compose  

🚀 **Objectif** : Offrir une plateforme sécurisée et ergonomique pour la location entre particuliers.  

📌 **Statut** : En cours de développement

