<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129142837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE genres (id INT AUTO_INCREMENT NOT NULL, name_eng VARCHAR(255) NOT NULL, name_fra VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genres_serie (genres_id INT NOT NULL, serie_id INT NOT NULL, INDEX IDX_C3AC1C606A3B2603 (genres_id), INDEX IDX_C3AC1C60D94388BD (serie_id), PRIMARY KEY(genres_id, serie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE genres_serie ADD CONSTRAINT FK_C3AC1C606A3B2603 FOREIGN KEY (genres_id) REFERENCES genres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genres_serie ADD CONSTRAINT FK_C3AC1C60D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE genres_serie DROP FOREIGN KEY FK_C3AC1C606A3B2603');
        $this->addSql('ALTER TABLE genres_serie DROP FOREIGN KEY FK_C3AC1C60D94388BD');
        $this->addSql('DROP TABLE genres');
        $this->addSql('DROP TABLE genres_serie');
    }
}
