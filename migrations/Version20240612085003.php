<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612085003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE serie_update (id INT AUTO_INCREMENT NOT NULL, serie_id INT NOT NULL, updated_at DATE NOT NULL, old_status VARCHAR(255) DEFAULT NULL, new_status VARCHAR(255) DEFAULT NULL, old_next_aired DATE DEFAULT NULL, new_next_aired DATE DEFAULT NULL, INDEX IDX_6F8F6E11D94388BD (serie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE serie_update ADD CONSTRAINT FK_6F8F6E11D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE serie_update DROP FOREIGN KEY FK_6F8F6E11D94388BD');
        $this->addSql('DROP TABLE serie_update');
    }
}
