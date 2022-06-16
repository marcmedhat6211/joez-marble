<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220616001618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `shipping_information` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, company_name VARCHAR(120) DEFAULT NULL, first_name VARCHAR(120) NOT NULL, last_name VARCHAR(120) NOT NULL, phone VARCHAR(120) NOT NULL, address VARCHAR(120) NOT NULL, district VARCHAR(120) NOT NULL, address_name VARCHAR(120) NOT NULL, UNIQUE INDEX UNIQ_A5129F20A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `shipping_information` ADD CONSTRAINT FK_A5129F20A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `shipping_information`');
    }
}
