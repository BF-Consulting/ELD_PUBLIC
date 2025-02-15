<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221106160805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acte_gestion (id INT AUTO_INCREMENT NOT NULL, id_gestionnaire_id INT NOT NULL, commercialisateur_id INT DEFAULT NULL, employeur_id INT DEFAULT NULL, type_population_id INT DEFAULT NULL, date_add DATETIME NOT NULL, departement VARCHAR(255) DEFAULT NULL, INDEX IDX_272A9C9C81DB6F93 (id_gestionnaire_id), INDEX IDX_272A9C9C7EF6BDAB (commercialisateur_id), INDEX IDX_272A9C9C5D7C53EC (employeur_id), INDEX IDX_272A9C9CB9B866D4 (type_population_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acte_gestion ADD CONSTRAINT FK_272A9C9C81DB6F93 FOREIGN KEY (id_gestionnaire_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE acte_gestion ADD CONSTRAINT FK_272A9C9C7EF6BDAB FOREIGN KEY (commercialisateur_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE acte_gestion ADD CONSTRAINT FK_272A9C9C5D7C53EC FOREIGN KEY (employeur_id) REFERENCES employeur (id)');
        $this->addSql('ALTER TABLE acte_gestion ADD CONSTRAINT FK_272A9C9CB9B866D4 FOREIGN KEY (type_population_id) REFERENCES type_population (id)');
        $this->addSql('DROP TABLE pensionner');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pensionner (id INT AUTO_INCREMENT NOT NULL, nni VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, surname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, genre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, situation_familliale VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, num_contrat VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, code_fournisseur VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, dernnier_employeur VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, etablissement VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date_gestion DATE DEFAULT NULL, medecin VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, statut_doctor VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, telephone_fixe VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, portable VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, num_secu VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date_inactiviter DATE DEFAULT NULL, date_naissance DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE acte_gestion DROP FOREIGN KEY FK_272A9C9C81DB6F93');
        $this->addSql('ALTER TABLE acte_gestion DROP FOREIGN KEY FK_272A9C9C7EF6BDAB');
        $this->addSql('ALTER TABLE acte_gestion DROP FOREIGN KEY FK_272A9C9C5D7C53EC');
        $this->addSql('ALTER TABLE acte_gestion DROP FOREIGN KEY FK_272A9C9CB9B866D4');
        $this->addSql('DROP TABLE acte_gestion');
    }
}
