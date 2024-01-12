<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240106135436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode_show ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE episode_show ADD CONSTRAINT FK_56D84521A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_56D84521A76ED395 ON episode_show (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode_show DROP FOREIGN KEY FK_56D84521A76ED395');
        $this->addSql('DROP INDEX IDX_56D84521A76ED395 ON episode_show');
        $this->addSql('ALTER TABLE episode_show DROP user_id');
    }
}
