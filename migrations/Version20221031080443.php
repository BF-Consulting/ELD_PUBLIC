<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221031080443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pensionner');
        $this->addSql('ALTER TABLE population DROP FOREIGN KEY FK_B449A008A60B31F8');
        $this->addSql('DROP INDEX IDX_B449A008A60B31F8 ON population');
        $this->addSql('ALTER TABLE population ADD commercialisateur_pdl2_id INT DEFAULT NULL, ADD commercialisateur_pdl3_id INT DEFAULT NULL, CHANGE commercialisateurs_id commercialisateur_pdl1_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE population ADD CONSTRAINT FK_B449A008512B5470 FOREIGN KEY (commercialisateur_pdl1_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE population ADD CONSTRAINT FK_B449A008439EFB9E FOREIGN KEY (commercialisateur_pdl2_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE population ADD CONSTRAINT FK_B449A008FB229CFB FOREIGN KEY (commercialisateur_pdl3_id) REFERENCES fournisseur (id)');
        $this->addSql('CREATE INDEX IDX_B449A008512B5470 ON population (commercialisateur_pdl1_id)');
        $this->addSql('CREATE INDEX IDX_B449A008439EFB9E ON population (commercialisateur_pdl2_id)');
        $this->addSql('CREATE INDEX IDX_B449A008FB229CFB ON population (commercialisateur_pdl3_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pensionner (id INT AUTO_INCREMENT NOT NULL, nni VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, surname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, genre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, situation_familliale VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, num_contrat VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, code_fournisseur VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, dernnier_employeur VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, etablissement VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date_gestion DATE DEFAULT NULL, medecin VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, statut_doctor VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, telephone_fixe VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, portable VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, num_secu VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date_inactiviter DATE DEFAULT NULL, date_naissance DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE population DROP FOREIGN KEY FK_B449A008512B5470');
        $this->addSql('ALTER TABLE population DROP FOREIGN KEY FK_B449A008439EFB9E');
        $this->addSql('ALTER TABLE population DROP FOREIGN KEY FK_B449A008FB229CFB');
        $this->addSql('DROP INDEX IDX_B449A008512B5470 ON population');
        $this->addSql('DROP INDEX IDX_B449A008439EFB9E ON population');
        $this->addSql('DROP INDEX IDX_B449A008FB229CFB ON population');
        $this->addSql('ALTER TABLE population ADD commercialisateurs_id INT DEFAULT NULL, DROP commercialisateur_pdl1_id, DROP commercialisateur_pdl2_id, DROP commercialisateur_pdl3_id');
        $this->addSql('ALTER TABLE population ADD CONSTRAINT FK_B449A008A60B31F8 FOREIGN KEY (commercialisateurs_id) REFERENCES fournisseur (id)');
        $this->addSql('CREATE INDEX IDX_B449A008A60B31F8 ON population (commercialisateurs_id)');
    }
}
