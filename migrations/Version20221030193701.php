<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221030193701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pensionner');
        $this->addSql('ALTER TABLE population DROP nom, DROP prenom, DROP nni, DROP num_contrat, DROP ref_pdl_pce, DROP adresse_livraison, DROP cp_livraison, DROP ville_livraison, DROP adresse_facturation, DROP cp_facturation, DROP ville_facturation, DROP num_facture_agent, DROP energie, DROP debut_periode_conso, DROP fin_periode_conso, DROP quantite_kwh, DROP tarif_kwh, DROP montant_taxe_energie, DROP montant_diff_tarifaire, DROP montant_total');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pensionner (id INT AUTO_INCREMENT NOT NULL, nni VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, surname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, genre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, situation_familliale VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, num_contrat VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, code_fournisseur VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, dernnier_employeur VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, etablissement VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date_gestion DATE DEFAULT NULL, medecin VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, statut_doctor VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, telephone_fixe VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, portable VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, num_secu VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date_inactiviter DATE DEFAULT NULL, date_naissance DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE population ADD nom VARCHAR(255) NOT NULL, ADD prenom VARCHAR(255) DEFAULT NULL, ADD nni VARCHAR(255) NOT NULL, ADD num_contrat VARCHAR(255) NOT NULL, ADD ref_pdl_pce VARCHAR(255) NOT NULL, ADD adresse_livraison VARCHAR(255) NOT NULL, ADD cp_livraison VARCHAR(255) NOT NULL, ADD ville_livraison VARCHAR(255) NOT NULL, ADD adresse_facturation VARCHAR(255) NOT NULL, ADD cp_facturation VARCHAR(255) NOT NULL, ADD ville_facturation VARCHAR(255) NOT NULL, ADD num_facture_agent VARCHAR(255) NOT NULL, ADD energie VARCHAR(255) NOT NULL, ADD debut_periode_conso DATE NOT NULL, ADD fin_periode_conso DATE NOT NULL, ADD quantite_kwh VARCHAR(255) NOT NULL, ADD tarif_kwh VARCHAR(255) NOT NULL, ADD montant_taxe_energie VARCHAR(255) NOT NULL, ADD montant_diff_tarifaire VARCHAR(255) NOT NULL, ADD montant_total VARCHAR(255) NOT NULL');
    }
}
