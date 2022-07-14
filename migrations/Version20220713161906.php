<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713161906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD header_image_one_id INT DEFAULT NULL, ADD header_image_two_id INT DEFAULT NULL, ADD cover_photo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1D937825C FOREIGN KEY (header_image_one_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1B26B6593 FOREIGN KEY (header_image_two_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A69B8AD7 FOREIGN KEY (cover_photo_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1D937825C ON category (header_image_one_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1B26B6593 ON category (header_image_two_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1A69B8AD7 ON category (cover_photo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1D937825C');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1B26B6593');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1A69B8AD7');
        $this->addSql('DROP INDEX UNIQ_64C19C1D937825C ON category');
        $this->addSql('DROP INDEX UNIQ_64C19C1B26B6593 ON category');
        $this->addSql('DROP INDEX UNIQ_64C19C1A69B8AD7 ON category');
        $this->addSql('ALTER TABLE category DROP header_image_one_id, DROP header_image_two_id, DROP cover_photo_id');
    }
}
