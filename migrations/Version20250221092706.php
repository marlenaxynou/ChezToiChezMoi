<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221092706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonce (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, titre VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, prix INT NOT NULL, adresse VARCHAR(100) NOT NULL, ville VARCHAR(50) NOT NULL, nb_personne INT NOT NULL, date_creation DATETIME NOT NULL, disponibilite DATETIME NOT NULL, INDEX IDX_F65593E5C6EE5C49 (id_utilisateur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, id_annonce INT NOT NULL, id_utilisateur INT NOT NULL, note INT NOT NULL, commentaire LONGTEXT DEFAULT NULL, date_avis DATETIME NOT NULL, INDEX IDX_8F91ABF02D8F2BF8 (id_annonce), INDEX IDX_8F91ABF0C6EE5C49 (id_utilisateur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, id_annonce INT DEFAULT NULL, chemin VARCHAR(255) NOT NULL, INDEX IDX_C53D045F2D8F2BF8 (id_annonce), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, id_annonce INT NOT NULL, id_utilisateur INT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_42C849552D8F2BF8 (id_annonce), INDEX IDX_42C84955C6EE5C49 (id_utilisateur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(150) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, date_inscription DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5C6EE5C49 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF02D8F2BF8 FOREIGN KEY (id_annonce) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0C6EE5C49 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F2D8F2BF8 FOREIGN KEY (id_annonce) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552D8F2BF8 FOREIGN KEY (id_annonce) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955C6EE5C49 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5C6EE5C49');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF02D8F2BF8');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0C6EE5C49');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F2D8F2BF8');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849552D8F2BF8');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955C6EE5C49');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
