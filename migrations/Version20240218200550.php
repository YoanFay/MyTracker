<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218200550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_game_platform (game_id INT NOT NULL, game_platform_id INT NOT NULL, INDEX IDX_38F2B386E48FD905 (game_id), INDEX IDX_38F2B38621B30B6D (game_platform_id), PRIMARY KEY(game_id, game_platform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_platform (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_game_platform ADD CONSTRAINT FK_38F2B386E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_platform ADD CONSTRAINT FK_38F2B38621B30B6D FOREIGN KEY (game_platform_id) REFERENCES game_platform (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_platforms DROP FOREIGN KEY FK_1FED1BADD45313CB');
        $this->addSql('ALTER TABLE game_game_platforms DROP FOREIGN KEY FK_1FED1BADE48FD905');
        $this->addSql('DROP TABLE game_game_platforms');
        $this->addSql('DROP TABLE game_platforms');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_game_platforms (game_id INT NOT NULL, game_platforms_id INT NOT NULL, INDEX IDX_1FED1BADE48FD905 (game_id), INDEX IDX_1FED1BADD45313CB (game_platforms_id), PRIMARY KEY(game_id, game_platforms_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE game_platforms (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE game_game_platforms ADD CONSTRAINT FK_1FED1BADD45313CB FOREIGN KEY (game_platforms_id) REFERENCES game_platforms (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_platforms ADD CONSTRAINT FK_1FED1BADE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_platform DROP FOREIGN KEY FK_38F2B386E48FD905');
        $this->addSql('ALTER TABLE game_game_platform DROP FOREIGN KEY FK_38F2B38621B30B6D');
        $this->addSql('DROP TABLE game_game_platform');
        $this->addSql('DROP TABLE game_platform');
    }
}
