<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426200621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE banner (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, placement INT NOT NULL, url VARCHAR(255) DEFAULT NULL, text TINYTEXT DEFAULT NULL, action_button_text VARCHAR(20) DEFAULT NULL, sort_no INT DEFAULT NULL, publish TINYINT(1) NOT NULL, open_new_tab TINYINT(1) NOT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, UNIQUE INDEX UNIQ_6F9DB8E73DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, living TINYINT(1) NOT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, flag_id INT DEFAULT NULL, code VARCHAR(50) NOT NULL, egp_equivalence DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_6956883F919FE4E5 (flag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faq (id INT AUTO_INCREMENT NOT NULL, faq_category_id INT DEFAULT NULL, question LONGTEXT NOT NULL, answer LONGTEXT NOT NULL, sort_no INT DEFAULT NULL, publish TINYINT(1) NOT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, INDEX IDX_E8FF75CCF689B0DB (faq_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faq_category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, sort_no INT DEFAULT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(220) NOT NULL, path VARCHAR(255) NOT NULL, alt VARCHAR(255) DEFAULT NULL, width DOUBLE PRECISION NOT NULL, height DOUBLE PRECISION NOT NULL, size DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, UNIQUE INDEX UNIQ_7CBE75953DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, seo_id INT DEFAULT NULL, subcategory_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, sku VARCHAR(50) NOT NULL, price DOUBLE PRECISION NOT NULL, brief LONGTEXT NOT NULL, description LONGTEXT NOT NULL, publish TINYINT(1) NOT NULL, featured TINYINT(1) NOT NULL, new_arrival TINYINT(1) NOT NULL, best_seller TINYINT(1) NOT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, UNIQUE INDEX UNIQ_D34A04AD97E3DD86 (seo_id), INDEX IDX_D34A04AD5DC6FE57 (subcategory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_image (product_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_64617F034584665A (product_id), INDEX IDX_64617F033DA5256D (image_id), PRIMARY KEY(product_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_material (product_id INT NOT NULL, material_id INT NOT NULL, INDEX IDX_B70E1F024584665A (product_id), INDEX IDX_B70E1F02E308AC6F (material_id), PRIMARY KEY(product_id, material_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_material_image (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, image_id INT DEFAULT NULL, material_id INT DEFAULT NULL, INDEX IDX_C0B3D63D4584665A (product_id), INDEX IDX_C0B3D63D3DA5256D (image_id), INDEX IDX_C0B3D63DE308AC6F (material_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_spec (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, value VARCHAR(120) NOT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, INDEX IDX_4DE6359F4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seo (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(250) NOT NULL, slug VARCHAR(250) NOT NULL, UNIQUE INDEX UNIQ_6C71EC30989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subcategory (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, INDEX IDX_DDCA44812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE testimonial (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, sort_no INT DEFAULT NULL, client VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, publish TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_E6BDCDF73DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, gender VARCHAR(20) DEFAULT NULL, birthdate DATETIME DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, facebook_id VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, last_login DATETIME DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', confirmation_token VARCHAR(255) DEFAULT NULL, deleted DATETIME DEFAULT NULL, deleted_by VARCHAR(30) DEFAULT NULL, created DATETIME NOT NULL, creator VARCHAR(255) NOT NULL, modified DATETIME NOT NULL, modified_by VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE banner ADD CONSTRAINT FK_6F9DB8E73DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE currency ADD CONSTRAINT FK_6956883F919FE4E5 FOREIGN KEY (flag_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE faq ADD CONSTRAINT FK_E8FF75CCF689B0DB FOREIGN KEY (faq_category_id) REFERENCES faq_category (id)');
        $this->addSql('ALTER TABLE material ADD CONSTRAINT FK_7CBE75953DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD97E3DD86 FOREIGN KEY (seo_id) REFERENCES seo (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD5DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id)');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F033DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_material ADD CONSTRAINT FK_B70E1F024584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_material ADD CONSTRAINT FK_B70E1F02E308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
        $this->addSql('ALTER TABLE product_material_image ADD CONSTRAINT FK_C0B3D63D4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_material_image ADD CONSTRAINT FK_C0B3D63D3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE product_material_image ADD CONSTRAINT FK_C0B3D63DE308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
        $this->addSql('ALTER TABLE product_spec ADD CONSTRAINT FK_4DE6359F4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE subcategory ADD CONSTRAINT FK_DDCA44812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE testimonial ADD CONSTRAINT FK_E6BDCDF73DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subcategory DROP FOREIGN KEY FK_DDCA44812469DE2');
        $this->addSql('ALTER TABLE faq DROP FOREIGN KEY FK_E8FF75CCF689B0DB');
        $this->addSql('ALTER TABLE banner DROP FOREIGN KEY FK_6F9DB8E73DA5256D');
        $this->addSql('ALTER TABLE currency DROP FOREIGN KEY FK_6956883F919FE4E5');
        $this->addSql('ALTER TABLE material DROP FOREIGN KEY FK_7CBE75953DA5256D');
        $this->addSql('ALTER TABLE product_image DROP FOREIGN KEY FK_64617F033DA5256D');
        $this->addSql('ALTER TABLE product_material_image DROP FOREIGN KEY FK_C0B3D63D3DA5256D');
        $this->addSql('ALTER TABLE testimonial DROP FOREIGN KEY FK_E6BDCDF73DA5256D');
        $this->addSql('ALTER TABLE product_material DROP FOREIGN KEY FK_B70E1F02E308AC6F');
        $this->addSql('ALTER TABLE product_material_image DROP FOREIGN KEY FK_C0B3D63DE308AC6F');
        $this->addSql('ALTER TABLE product_image DROP FOREIGN KEY FK_64617F034584665A');
        $this->addSql('ALTER TABLE product_material DROP FOREIGN KEY FK_B70E1F024584665A');
        $this->addSql('ALTER TABLE product_material_image DROP FOREIGN KEY FK_C0B3D63D4584665A');
        $this->addSql('ALTER TABLE product_spec DROP FOREIGN KEY FK_4DE6359F4584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD97E3DD86');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD5DC6FE57');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE banner');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE faq');
        $this->addSql('DROP TABLE faq_category');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('DROP TABLE product_material');
        $this->addSql('DROP TABLE product_material_image');
        $this->addSql('DROP TABLE product_spec');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE seo');
        $this->addSql('DROP TABLE subcategory');
        $this->addSql('DROP TABLE testimonial');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
