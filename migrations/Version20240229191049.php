<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229191049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game CHANGE imdb_id igdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_developer CHANGE imdb_id igdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_genre CHANGE imdb_id igdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_mode CHANGE imdb_id igdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_platform CHANGE imdb_id igdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_publishers CHANGE imdb_id igdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_serie CHANGE imdb_id igdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_theme CHANGE imdb_id igdb_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game CHANGE igdb_id imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_developer CHANGE igdb_id imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_genre CHANGE igdb_id imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_mode CHANGE igdb_id imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_platform CHANGE igdb_id imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_publishers CHANGE igdb_id imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_serie CHANGE igdb_id imdb_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_theme CHANGE igdb_id imdb_id INT NOT NULL');
    }
}
