<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240716124846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, tvdb_id INT DEFAULT NULL, started_at DATE DEFAULT NULL, type VARCHAR(255) NOT NULL, country VARCHAR(255) DEFAULT NULL, INDEX IDX_4FBF094F727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_company (serie_id INT NOT NULL, company_id INT NOT NULL, INDEX IDX_7B267AE8D94388BD (serie_id), INDEX IDX_7B267AE8979B1AD6 (company_id), PRIMARY KEY(serie_id, company_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F727ACA70 FOREIGN KEY (parent_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE serie_company ADD CONSTRAINT FK_7B267AE8D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_company ADD CONSTRAINT FK_7B267AE8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F727ACA70');
        $this->addSql('ALTER TABLE serie_company DROP FOREIGN KEY FK_7B267AE8D94388BD');
        $this->addSql('ALTER TABLE serie_company DROP FOREIGN KEY FK_7B267AE8979B1AD6');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE serie_company');
    }
}
