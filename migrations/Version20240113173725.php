<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240113173725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movie ADD plex_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE movie RENAME INDEX idx_1d5ef26f9d86650f TO IDX_1D5EF26FA76ED395');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movie DROP plex_id');
        $this->addSql('ALTER TABLE movie RENAME INDEX idx_1d5ef26fa76ed395 TO IDX_1D5EF26F9D86650F');
    }
}
