<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221020074922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE population (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) DEFAULT NULL, nni VARCHAR(255) NOT NULL, num_contrat VARCHAR(255) NOT NULL, ref_pdl_pce VARCHAR(255) NOT NULL, adresse_livraison VARCHAR(255) NOT NULL, cp_livraison VARCHAR(255) NOT NULL, ville_livraison VARCHAR(255) NOT NULL, adresse_facturation VARCHAR(255) NOT NULL, cp_facturation VARCHAR(255) NOT NULL, ville_facturation VARCHAR(255) NOT NULL, num_facture_agent VARCHAR(255) NOT NULL, energie VARCHAR(255) NOT NULL, debut_periode_conso DATE NOT NULL, fin_periode_conso DATE NOT NULL, quantite_kwh VARCHAR(255) NOT NULL, tarif_kwh VARCHAR(255) NOT NULL, montant_taxe_energie VARCHAR(255) NOT NULL, montant_diff_tarifaire VARCHAR(255) NOT NULL, montant_total VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE pensionner');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE population');
    }
}
