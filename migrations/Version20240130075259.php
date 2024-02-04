<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130075259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, tags_type_id INT NOT NULL, name_eng VARCHAR(255) NOT NULL, name_fra VARCHAR(255) DEFAULT NULL, INDEX IDX_6FBC9426E3F06782 (tags_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags_serie (tags_id INT NOT NULL, serie_id INT NOT NULL, INDEX IDX_C15E656C8D7B4FB4 (tags_id), INDEX IDX_C15E656CD94388BD (serie_id), PRIMARY KEY(tags_id, serie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags_type (id INT AUTO_INCREMENT NOT NULL, name_eng VARCHAR(255) DEFAULT NULL, name_fra VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC9426E3F06782 FOREIGN KEY (tags_type_id) REFERENCES tags_type (id)');
        $this->addSql('ALTER TABLE tags_serie ADD CONSTRAINT FK_C15E656C8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags_serie ADD CONSTRAINT FK_C15E656CD94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC9426E3F06782');
        $this->addSql('ALTER TABLE tags_serie DROP FOREIGN KEY FK_C15E656C8D7B4FB4');
        $this->addSql('ALTER TABLE tags_serie DROP FOREIGN KEY FK_C15E656CD94388BD');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tags_serie');
        $this->addSql('DROP TABLE tags_type');
    }
}
