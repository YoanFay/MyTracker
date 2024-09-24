<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240924072043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE episode_show (id INT AUTO_INCREMENT NOT NULL, episode_id INT NOT NULL, show_date DATETIME NOT NULL, INDEX IDX_56D84521362B62A0 (episode_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE episode_show ADD CONSTRAINT FK_56D84521362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id)');
        $this->addSql('ALTER TABLE episode CHANGE show_date show_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE episode RENAME INDEX idx_56d84521d94388bd TO IDX_DDAA1CDAD94388BD');
        $this->addSql('ALTER TABLE episode RENAME INDEX idx_56d84521a76ed395 TO IDX_DDAA1CDAA76ED395');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode_show DROP FOREIGN KEY FK_56D84521362B62A0');
        $this->addSql('DROP TABLE episode_show');
        $this->addSql('ALTER TABLE episode CHANGE show_date show_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE episode RENAME INDEX idx_ddaa1cdaa76ed395 TO IDX_56D84521A76ED395');
        $this->addSql('ALTER TABLE episode RENAME INDEX idx_ddaa1cdad94388bd TO IDX_56D84521D94388BD');
    }
}
