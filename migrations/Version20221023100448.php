<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023100448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pensionner');
        $this->addSql('ALTER TABLE population ADD commercialisateurs_id INT DEFAULT NULL, ADD employeur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE population ADD CONSTRAINT FK_B449A008A60B31F8 FOREIGN KEY (commercialisateurs_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE population ADD CONSTRAINT FK_B449A0085D7C53EC FOREIGN KEY (employeur_id) REFERENCES employeur (id)');
        $this->addSql('CREATE INDEX IDX_B449A008A60B31F8 ON population (commercialisateurs_id)');
        $this->addSql('CREATE INDEX IDX_B449A0085D7C53EC ON population (employeur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE population DROP FOREIGN KEY FK_B449A008A60B31F8');
        $this->addSql('ALTER TABLE population DROP FOREIGN KEY FK_B449A0085D7C53EC');
        $this->addSql('DROP INDEX IDX_B449A008A60B31F8 ON population');
        $this->addSql('DROP INDEX IDX_B449A0085D7C53EC ON population');
        $this->addSql('ALTER TABLE population DROP commercialisateurs_id, DROP employeur_id');
    }
}
