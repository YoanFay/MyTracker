<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240106135336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode_show ADD serie_id INT NOT NULL');
        $this->addSql('ALTER TABLE episode_show ADD CONSTRAINT FK_56D84521D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id)');
        $this->addSql('CREATE INDEX IDX_56D84521D94388BD ON episode_show (serie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode_show DROP FOREIGN KEY FK_56D84521D94388BD');
        $this->addSql('DROP INDEX IDX_56D84521D94388BD ON episode_show');
        $this->addSql('ALTER TABLE episode_show DROP serie_id');
    }
}
