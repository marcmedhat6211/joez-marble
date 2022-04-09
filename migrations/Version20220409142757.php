<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220409142757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, UNIQUE INDEX UNIQ_7CBE75953DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE material_image (material_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_93DAA811E308AC6F (material_id), INDEX IDX_93DAA8113DA5256D (image_id), PRIMARY KEY(material_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE material ADD CONSTRAINT FK_7CBE75953DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE material_image ADD CONSTRAINT FK_93DAA811E308AC6F FOREIGN KEY (material_id) REFERENCES material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE material_image ADD CONSTRAINT FK_93DAA8113DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD3DA5256D');
        $this->addSql('DROP INDEX UNIQ_D34A04AD3DA5256D ON product');
        $this->addSql('ALTER TABLE product DROP image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE material_image DROP FOREIGN KEY FK_93DAA811E308AC6F');
        $this->addSql('CREATE TABLE product_image (product_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_64617F033DA5256D (image_id), INDEX IDX_64617F034584665A (product_id), PRIMARY KEY(product_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F033DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE material_image');
        $this->addSql('ALTER TABLE banner CHANGE title title VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE url url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE text text TINYTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE action_button_text action_button_text VARCHAR(20) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creator creator VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE modified_by modified_by VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_by deleted_by VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE category CHANGE title title VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creator creator VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE modified_by modified_by VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_by deleted_by VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE currency CHANGE code code VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE image CHANGE name name VARCHAR(220) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE path path VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE alt alt VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE type type VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE product ADD image_id INT DEFAULT NULL, CHANGE title title VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sku sku VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE brief brief LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creator creator VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE modified_by modified_by VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_by deleted_by VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD3DA5256D ON product (image_id)');
        $this->addSql('ALTER TABLE product_spec CHANGE title title VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE value value VARCHAR(120) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creator creator VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE modified_by modified_by VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_by deleted_by VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE reset_password_request CHANGE selector selector VARCHAR(20) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE hashed_token hashed_token VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE subcategory CHANGE title title VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creator creator VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE modified_by modified_by VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_by deleted_by VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE testimonial CHANGE url url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE client client VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE message message LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE `user` CHANGE full_name full_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE gender gender VARCHAR(20) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE facebook_id facebook_id VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE google_id google_id VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_by deleted_by VARCHAR(30) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE creator creator VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE modified_by modified_by VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
