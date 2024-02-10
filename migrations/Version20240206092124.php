<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206092124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE manga (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, author_id INT NOT NULL, editor_id INT NOT NULL, name VARCHAR(255) NOT NULL, release_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, nb_tomes INT DEFAULT NULL, INDEX IDX_765A9E03C54C8C93 (type_id), INDEX IDX_765A9E03F675F31B (author_id), INDEX IDX_765A9E036995AC4C (editor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_manga_genre (manga_id INT NOT NULL, manga_genre_id INT NOT NULL, INDEX IDX_9ACBD91D7B6461 (manga_id), INDEX IDX_9ACBD91D350F545C (manga_genre_id), PRIMARY KEY(manga_id, manga_genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_manga_theme (manga_id INT NOT NULL, manga_theme_id INT NOT NULL, INDEX IDX_8EEE0DED7B6461 (manga_id), INDEX IDX_8EEE0DED2E9BF3C4 (manga_theme_id), PRIMARY KEY(manga_id, manga_theme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_editor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_tome (id INT AUTO_INCREMENT NOT NULL, manga_id INT NOT NULL, tome_number INT NOT NULL, page INT NOT NULL, release_date DATETIME NOT NULL, reading_start_date DATETIME NOT NULL, reading_end_date DATETIME DEFAULT NULL, INDEX IDX_344D0DBD7B6461 (manga_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE manga ADD CONSTRAINT FK_765A9E03C54C8C93 FOREIGN KEY (type_id) REFERENCES manga_type (id)');
        $this->addSql('ALTER TABLE manga ADD CONSTRAINT FK_765A9E03F675F31B FOREIGN KEY (author_id) REFERENCES manga_author (id)');
        $this->addSql('ALTER TABLE manga ADD CONSTRAINT FK_765A9E036995AC4C FOREIGN KEY (editor_id) REFERENCES manga_editor (id)');
        $this->addSql('ALTER TABLE manga_manga_genre ADD CONSTRAINT FK_9ACBD91D7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manga_manga_genre ADD CONSTRAINT FK_9ACBD91D350F545C FOREIGN KEY (manga_genre_id) REFERENCES manga_genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manga_manga_theme ADD CONSTRAINT FK_8EEE0DED7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manga_manga_theme ADD CONSTRAINT FK_8EEE0DED2E9BF3C4 FOREIGN KEY (manga_theme_id) REFERENCES manga_theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manga_tome ADD CONSTRAINT FK_344D0DBD7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE manga DROP FOREIGN KEY FK_765A9E03C54C8C93');
        $this->addSql('ALTER TABLE manga DROP FOREIGN KEY FK_765A9E03F675F31B');
        $this->addSql('ALTER TABLE manga DROP FOREIGN KEY FK_765A9E036995AC4C');
        $this->addSql('ALTER TABLE manga_manga_genre DROP FOREIGN KEY FK_9ACBD91D7B6461');
        $this->addSql('ALTER TABLE manga_manga_genre DROP FOREIGN KEY FK_9ACBD91D350F545C');
        $this->addSql('ALTER TABLE manga_manga_theme DROP FOREIGN KEY FK_8EEE0DED7B6461');
        $this->addSql('ALTER TABLE manga_manga_theme DROP FOREIGN KEY FK_8EEE0DED2E9BF3C4');
        $this->addSql('ALTER TABLE manga_tome DROP FOREIGN KEY FK_344D0DBD7B6461');
        $this->addSql('DROP TABLE manga');
        $this->addSql('DROP TABLE manga_manga_genre');
        $this->addSql('DROP TABLE manga_manga_theme');
        $this->addSql('DROP TABLE manga_author');
        $this->addSql('DROP TABLE manga_editor');
        $this->addSql('DROP TABLE manga_genre');
        $this->addSql('DROP TABLE manga_theme');
        $this->addSql('DROP TABLE manga_tome');
        $this->addSql('DROP TABLE manga_type');
    }
}
