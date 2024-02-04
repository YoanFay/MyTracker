<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130094519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE genres CHANGE name_eng name_eng VARCHAR(255) DEFAULT NULL, CHANGE name_fra name_fra VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tags CHANGE name_eng name_eng VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE genres CHANGE name_eng name_eng VARCHAR(255) NOT NULL, CHANGE name_fra name_fra VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tags CHANGE name_eng name_eng VARCHAR(255) NOT NULL');
    }
}
