<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812171826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE music (id INT AUTO_INCREMENT NOT NULL, music_artist_id INT NOT NULL, name VARCHAR(255) NOT NULL, duration INT DEFAULT NULL, mbid VARCHAR(255) DEFAULT NULL, INDEX IDX_CD52224A655D9A59 (music_artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE music_music_tags (music_id INT NOT NULL, music_tags_id INT NOT NULL, INDEX IDX_9A8EC955399BBB13 (music_id), INDEX IDX_9A8EC955498FDDCF (music_tags_id), PRIMARY KEY(music_id, music_tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE music_artist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE music_listen (id INT AUTO_INCREMENT NOT NULL, music_id INT NOT NULL, listen_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_57FCA404399BBB13 (music_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE music_tags (id INT AUTO_INCREMENT NOT NULL, plex_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE music ADD CONSTRAINT FK_CD52224A655D9A59 FOREIGN KEY (music_artist_id) REFERENCES music_artist (id)');
        $this->addSql('ALTER TABLE music_music_tags ADD CONSTRAINT FK_9A8EC955399BBB13 FOREIGN KEY (music_id) REFERENCES music (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE music_music_tags ADD CONSTRAINT FK_9A8EC955498FDDCF FOREIGN KEY (music_tags_id) REFERENCES music_tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE music_listen ADD CONSTRAINT FK_57FCA404399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE music DROP FOREIGN KEY FK_CD52224A655D9A59');
        $this->addSql('ALTER TABLE music_music_tags DROP FOREIGN KEY FK_9A8EC955399BBB13');
        $this->addSql('ALTER TABLE music_music_tags DROP FOREIGN KEY FK_9A8EC955498FDDCF');
        $this->addSql('ALTER TABLE music_listen DROP FOREIGN KEY FK_57FCA404399BBB13');
        $this->addSql('DROP TABLE music');
        $this->addSql('DROP TABLE music_music_tags');
        $this->addSql('DROP TABLE music_artist');
        $this->addSql('DROP TABLE music_listen');
        $this->addSql('DROP TABLE music_tags');
    }
}
