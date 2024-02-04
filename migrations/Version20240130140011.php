<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130140011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE anime_genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE anime_genre_serie (anime_genre_id INT NOT NULL, serie_id INT NOT NULL, INDEX IDX_A8C2A0FA7560135 (anime_genre_id), INDEX IDX_A8C2A0FD94388BD (serie_id), PRIMARY KEY(anime_genre_id, serie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE anime_theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE anime_theme_serie (anime_theme_id INT NOT NULL, serie_id INT NOT NULL, INDEX IDX_EFA8BE2FBCC2A6AD (anime_theme_id), INDEX IDX_EFA8BE2FD94388BD (serie_id), PRIMARY KEY(anime_theme_id, serie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE anime_genre_serie ADD CONSTRAINT FK_A8C2A0FA7560135 FOREIGN KEY (anime_genre_id) REFERENCES anime_genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE anime_genre_serie ADD CONSTRAINT FK_A8C2A0FD94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE anime_theme_serie ADD CONSTRAINT FK_EFA8BE2FBCC2A6AD FOREIGN KEY (anime_theme_id) REFERENCES anime_theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE anime_theme_serie ADD CONSTRAINT FK_EFA8BE2FD94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anime_genre_serie DROP FOREIGN KEY FK_A8C2A0FA7560135');
        $this->addSql('ALTER TABLE anime_genre_serie DROP FOREIGN KEY FK_A8C2A0FD94388BD');
        $this->addSql('ALTER TABLE anime_theme_serie DROP FOREIGN KEY FK_EFA8BE2FBCC2A6AD');
        $this->addSql('ALTER TABLE anime_theme_serie DROP FOREIGN KEY FK_EFA8BE2FD94388BD');
        $this->addSql('DROP TABLE anime_genre');
        $this->addSql('DROP TABLE anime_genre_serie');
        $this->addSql('DROP TABLE anime_theme');
        $this->addSql('DROP TABLE anime_theme_serie');
    }
}
