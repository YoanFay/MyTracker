<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240225212246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movie_genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_genre_movie (movie_genre_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_8FDE615D9E604892 (movie_genre_id), INDEX IDX_8FDE615D8F93B6FC (movie_id), PRIMARY KEY(movie_genre_id, movie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movie_genre_movie ADD CONSTRAINT FK_8FDE615D9E604892 FOREIGN KEY (movie_genre_id) REFERENCES movie_genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_genre_movie ADD CONSTRAINT FK_8FDE615D8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie CHANGE tvdb_id tmdb_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movie_genre_movie DROP FOREIGN KEY FK_8FDE615D9E604892');
        $this->addSql('ALTER TABLE movie_genre_movie DROP FOREIGN KEY FK_8FDE615D8F93B6FC');
        $this->addSql('DROP TABLE movie_genre');
        $this->addSql('DROP TABLE movie_genre_movie');
        $this->addSql('ALTER TABLE movie CHANGE tmdb_id tvdb_id INT DEFAULT NULL');
    }
}
