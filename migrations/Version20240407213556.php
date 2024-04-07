<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407213556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE serie ADD serie_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE serie ADD CONSTRAINT FK_AA3A9334F1D5FF34 FOREIGN KEY (serie_type_id) REFERENCES serie_type (id)');
        $this->addSql('CREATE INDEX IDX_AA3A9334F1D5FF34 ON serie (serie_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE serie DROP FOREIGN KEY FK_AA3A9334F1D5FF34');
        $this->addSql('DROP INDEX IDX_AA3A9334F1D5FF34 ON serie');
        $this->addSql('ALTER TABLE serie DROP serie_type_id');
    }
}
