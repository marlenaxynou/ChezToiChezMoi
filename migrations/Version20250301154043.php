<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301154043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5C6EE5C49');
        $this->addSql('DROP INDEX IDX_F65593E5C6EE5C49 ON annonce');
        $this->addSql('ALTER TABLE annonce CHANGE id_utilisateur id_utilisateur INT NOT NULL');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5C6EE5C49 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_F65593E5C6EE5C49 ON annonce (id_utilisateur)');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF02D8F2BF8');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0C6EE5C49');
        $this->addSql('DROP INDEX IDX_8F91ABF02D8F2BF8 ON avis');
        $this->addSql('DROP INDEX IDX_8F91ABF0C6EE5C49 ON avis');
        $this->addSql('ALTER TABLE avis ADD id_annonce INT NOT NULL, ADD id_utilisateur INT NOT NULL, DROP id_annonce, DROP id_utilisateur');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF02D8F2BF8 FOREIGN KEY (id_annonce) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0C6EE5C49 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF02D8F2BF8 ON avis (id_annonce)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0C6EE5C49 ON avis (id_utilisateur)');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F2D8F2BF8');
        $this->addSql('DROP INDEX IDX_C53D045F2D8F2BF8 ON image');
        $this->addSql('ALTER TABLE image CHANGE id_annonce id_annonce INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F2D8F2BF8 FOREIGN KEY (id_annonce) REFERENCES annonce (id)');
        $this->addSql('CREATE INDEX IDX_C53D045F2D8F2BF8 ON image (id_annonce)');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849552D8F2BF8');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955C6EE5C49');
        $this->addSql('DROP INDEX IDX_42C849552D8F2BF8 ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955C6EE5C49 ON reservation');
        $this->addSql('ALTER TABLE reservation ADD id_annonce INT NOT NULL, ADD id_utilisateur INT NOT NULL, DROP id_annonce, DROP id_utilisateur');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552D8F2BF8 FOREIGN KEY (id_annonce) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955C6EE5C49 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_42C849552D8F2BF8 ON reservation (id_annonce)');
        $this->addSql('CREATE INDEX IDX_42C84955C6EE5C49 ON reservation (id_utilisateur)');
        $this->addSql('ALTER TABLE utilisateur ADD verified TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5C6EE5C49');
        $this->addSql('DROP INDEX IDX_F65593E5C6EE5C49 ON annonce');
        $this->addSql('ALTER TABLE annonce CHANGE id_utilisateur id_utilisateur INT NOT NULL');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5C6EE5C49 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F65593E5C6EE5C49 ON annonce (id_utilisateur)');
        $this->addSql('ALTER TABLE utilisateur DROP verified');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849552D8F2BF8');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955C6EE5C49');
        $this->addSql('DROP INDEX IDX_42C849552D8F2BF8 ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955C6EE5C49 ON reservation');
        $this->addSql('ALTER TABLE reservation ADD id_annonce INT NOT NULL, ADD id_utilisateur INT NOT NULL, DROP id_annonce, DROP id_utilisateur');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552D8F2BF8 FOREIGN KEY (id_annonce) REFERENCES annonce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955C6EE5C49 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_42C849552D8F2BF8 ON reservation (id_annonce)');
        $this->addSql('CREATE INDEX IDX_42C84955C6EE5C49 ON reservation (id_utilisateur)');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F2D8F2BF8');
        $this->addSql('DROP INDEX IDX_C53D045F2D8F2BF8 ON image');
        $this->addSql('ALTER TABLE image CHANGE id_annonce id_annonce INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F2D8F2BF8 FOREIGN KEY (id_annonce) REFERENCES annonce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C53D045F2D8F2BF8 ON image (id_annonce)');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF02D8F2BF8');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0C6EE5C49');
        $this->addSql('DROP INDEX IDX_8F91ABF02D8F2BF8 ON avis');
        $this->addSql('DROP INDEX IDX_8F91ABF0C6EE5C49 ON avis');
        $this->addSql('ALTER TABLE avis ADD id_annonce INT NOT NULL, ADD id_utilisateur INT NOT NULL, DROP id_annonce, DROP id_utilisateur');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF02D8F2BF8 FOREIGN KEY (id_annonce) REFERENCES annonce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0C6EE5C49 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8F91ABF02D8F2BF8 ON avis (id_annonce)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0C6EE5C49 ON avis (id_utilisateur)');
    }
}
