<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226164352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_game_developer (game_id INT NOT NULL, game_developer_id INT NOT NULL, INDEX IDX_6D9716D4E48FD905 (game_id), INDEX IDX_6D9716D48A0DA609 (game_developer_id), PRIMARY KEY(game_id, game_developer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_game_developer ADD CONSTRAINT FK_6D9716D4E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_developer ADD CONSTRAINT FK_6D9716D48A0DA609 FOREIGN KEY (game_developer_id) REFERENCES game_developer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C64DD9267');
        $this->addSql('DROP INDEX IDX_232B318C64DD9267 ON game');
        $this->addSql('ALTER TABLE game DROP developer_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_game_developer DROP FOREIGN KEY FK_6D9716D4E48FD905');
        $this->addSql('ALTER TABLE game_game_developer DROP FOREIGN KEY FK_6D9716D48A0DA609');
        $this->addSql('DROP TABLE game_game_developer');
        $this->addSql('ALTER TABLE game ADD developer_id INT NOT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C64DD9267 FOREIGN KEY (developer_id) REFERENCES game_developer (id)');
        $this->addSql('CREATE INDEX IDX_232B318C64DD9267 ON game (developer_id)');
    }
}
