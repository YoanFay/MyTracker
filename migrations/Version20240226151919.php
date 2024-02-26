<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226151919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_developer ADD imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_genre ADD imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_mode ADD imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_platform ADD imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_publishers ADD imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_serie ADD imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_theme ADD imdb_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_developer DROP imdb_id');
        $this->addSql('ALTER TABLE game_genre DROP imdb_id');
        $this->addSql('ALTER TABLE game_mode DROP imdb_id');
        $this->addSql('ALTER TABLE game_platform DROP imdb_id');
        $this->addSql('ALTER TABLE game_publishers DROP imdb_id');
        $this->addSql('ALTER TABLE game_serie DROP imdb_id');
        $this->addSql('ALTER TABLE game_theme DROP imdb_id');
    }
}
