<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220182016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_tracker CHANGE start_date start_date DATETIME NOT NULL, CHANGE end_date end_date DATETIME DEFAULT NULL, CHANGE complete_date complete_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_tracker CHANGE start_date start_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE end_date end_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE complete_date complete_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'');
    }
}
