<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240717111418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artwork (id INT AUTO_INCREMENT NOT NULL, api_id INT DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, language VARCHAR(5) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, text TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE serie ADD artwork_id INT DEFAULT NULL, DROP artwork, DROP vf_artwork');
        $this->addSql('ALTER TABLE serie ADD CONSTRAINT FK_AA3A9334DB8FFA4 FOREIGN KEY (artwork_id) REFERENCES artwork (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA3A9334DB8FFA4 ON serie (artwork_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE serie DROP FOREIGN KEY FK_AA3A9334DB8FFA4');
        $this->addSql('DROP TABLE artwork');
        $this->addSql('DROP INDEX UNIQ_AA3A9334DB8FFA4 ON serie');
        $this->addSql('ALTER TABLE serie ADD artwork VARCHAR(255) DEFAULT NULL, ADD vf_artwork TINYINT(1) NOT NULL, DROP artwork_id');
    }
}
