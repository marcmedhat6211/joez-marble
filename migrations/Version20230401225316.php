<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230401225316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gift (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, image_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, INDEX IDX_A47C990DA76ED395 (user_id), INDEX IDX_A47C990D3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990D3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990DA76ED395');
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990D3DA5256D');
        $this->addSql('DROP TABLE gift');
    }
}
