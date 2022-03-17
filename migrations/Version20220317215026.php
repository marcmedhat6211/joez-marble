<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220317215026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE banner (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) DEFAULT NULL, placement INT NOT NULL, url VARCHAR(255) DEFAULT NULL, text TINYTEXT DEFAULT NULL, action_button_text VARCHAR(20) DEFAULT NULL, sort_no INT DEFAULT NULL, publish TINYINT(1) NOT NULL, open_new_tab TINYINT(1) NOT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE banner');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE reset_password_request CHANGE selector selector VARCHAR(20) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE hashed_token hashed_token VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE `user` CHANGE full_name full_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE gender gender VARCHAR(20) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE facebook_id facebook_id VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE google_id google_id VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_by deleted_by VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creator creator VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE modified_by modified_by VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
