<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218095510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, developer_id INT NOT NULL, serie_id INT NOT NULL, name VARCHAR(255) NOT NULL, release_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_232B318C64DD9267 (developer_id), INDEX IDX_232B318CD94388BD (serie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_game_publishers (game_id INT NOT NULL, game_publishers_id INT NOT NULL, INDEX IDX_1541C371E48FD905 (game_id), INDEX IDX_1541C37131090696 (game_publishers_id), PRIMARY KEY(game_id, game_publishers_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_game_mode (game_id INT NOT NULL, game_mode_id INT NOT NULL, INDEX IDX_AE79EA85E48FD905 (game_id), INDEX IDX_AE79EA85E227FA65 (game_mode_id), PRIMARY KEY(game_id, game_mode_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_game_platforms (game_id INT NOT NULL, game_platforms_id INT NOT NULL, INDEX IDX_1FED1BADE48FD905 (game_id), INDEX IDX_1FED1BADD45313CB (game_platforms_id), PRIMARY KEY(game_id, game_platforms_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_game_theme (game_id INT NOT NULL, game_theme_id INT NOT NULL, INDEX IDX_4B9A596AE48FD905 (game_id), INDEX IDX_4B9A596AF2468BA1 (game_theme_id), PRIMARY KEY(game_id, game_theme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_game_genre (game_id INT NOT NULL, game_genre_id INT NOT NULL, INDEX IDX_5FBF8D9AE48FD905 (game_id), INDEX IDX_5FBF8D9AE9D22C39 (game_genre_id), PRIMARY KEY(game_id, game_genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_developer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_mode (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_platforms (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_publishers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_serie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_tracker (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, start_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', end_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', complete_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', end_time INT DEFAULT NULL, complete_time INT DEFAULT NULL, INDEX IDX_39A15580E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C64DD9267 FOREIGN KEY (developer_id) REFERENCES game_developer (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CD94388BD FOREIGN KEY (serie_id) REFERENCES game_serie (id)');
        $this->addSql('ALTER TABLE game_game_publishers ADD CONSTRAINT FK_1541C371E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_publishers ADD CONSTRAINT FK_1541C37131090696 FOREIGN KEY (game_publishers_id) REFERENCES game_publishers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_mode ADD CONSTRAINT FK_AE79EA85E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_mode ADD CONSTRAINT FK_AE79EA85E227FA65 FOREIGN KEY (game_mode_id) REFERENCES game_mode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_platforms ADD CONSTRAINT FK_1FED1BADE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_platforms ADD CONSTRAINT FK_1FED1BADD45313CB FOREIGN KEY (game_platforms_id) REFERENCES game_platforms (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_theme ADD CONSTRAINT FK_4B9A596AE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_theme ADD CONSTRAINT FK_4B9A596AF2468BA1 FOREIGN KEY (game_theme_id) REFERENCES game_theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_genre ADD CONSTRAINT FK_5FBF8D9AE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_genre ADD CONSTRAINT FK_5FBF8D9AE9D22C39 FOREIGN KEY (game_genre_id) REFERENCES game_genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_tracker ADD CONSTRAINT FK_39A15580E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C64DD9267');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CD94388BD');
        $this->addSql('ALTER TABLE game_game_publishers DROP FOREIGN KEY FK_1541C371E48FD905');
        $this->addSql('ALTER TABLE game_game_publishers DROP FOREIGN KEY FK_1541C37131090696');
        $this->addSql('ALTER TABLE game_game_mode DROP FOREIGN KEY FK_AE79EA85E48FD905');
        $this->addSql('ALTER TABLE game_game_mode DROP FOREIGN KEY FK_AE79EA85E227FA65');
        $this->addSql('ALTER TABLE game_game_platforms DROP FOREIGN KEY FK_1FED1BADE48FD905');
        $this->addSql('ALTER TABLE game_game_platforms DROP FOREIGN KEY FK_1FED1BADD45313CB');
        $this->addSql('ALTER TABLE game_game_theme DROP FOREIGN KEY FK_4B9A596AE48FD905');
        $this->addSql('ALTER TABLE game_game_theme DROP FOREIGN KEY FK_4B9A596AF2468BA1');
        $this->addSql('ALTER TABLE game_game_genre DROP FOREIGN KEY FK_5FBF8D9AE48FD905');
        $this->addSql('ALTER TABLE game_game_genre DROP FOREIGN KEY FK_5FBF8D9AE9D22C39');
        $this->addSql('ALTER TABLE game_tracker DROP FOREIGN KEY FK_39A15580E48FD905');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_game_publishers');
        $this->addSql('DROP TABLE game_game_mode');
        $this->addSql('DROP TABLE game_game_platforms');
        $this->addSql('DROP TABLE game_game_theme');
        $this->addSql('DROP TABLE game_game_genre');
        $this->addSql('DROP TABLE game_developer');
        $this->addSql('DROP TABLE game_genre');
        $this->addSql('DROP TABLE game_mode');
        $this->addSql('DROP TABLE game_platforms');
        $this->addSql('DROP TABLE game_publishers');
        $this->addSql('DROP TABLE game_serie');
        $this->addSql('DROP TABLE game_theme');
        $this->addSql('DROP TABLE game_tracker');
    }
}
