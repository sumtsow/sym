<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220611093116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE av_parameter (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5A3426C3C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, vendor_id INT NOT NULL, type_id INT NOT NULL, name VARCHAR(511) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_92FB68EF603EE73 (vendor_id), INDEX IDX_92FB68EC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE param_option (id INT AUTO_INCREMENT NOT NULL, av_parameter_id INT NOT NULL, value VARCHAR(1023) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_256430617C29AE2 (av_parameter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parameter (id INT AUTO_INCREMENT NOT NULL, device_id INT NOT NULL, value_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2A97911094A4C7D4 (device_id), INDEX IDX_2A979110F920BBA2 (value_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vendor (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F52233F6F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE av_parameter ADD CONSTRAINT FK_5A3426C3C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EF603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE param_option ADD CONSTRAINT FK_256430617C29AE2 FOREIGN KEY (av_parameter_id) REFERENCES av_parameter (id)');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A97911094A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A979110F920BBA2 FOREIGN KEY (value_id) REFERENCES param_option (id)');
        $this->addSql('ALTER TABLE vendor ADD CONSTRAINT FK_F52233F6F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE param_option DROP FOREIGN KEY FK_256430617C29AE2');
        $this->addSql('ALTER TABLE parameter DROP FOREIGN KEY FK_2A97911094A4C7D4');
        $this->addSql('ALTER TABLE parameter DROP FOREIGN KEY FK_2A979110F920BBA2');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68EF603EE73');
        $this->addSql('DROP TABLE av_parameter');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE param_option');
        $this->addSql('DROP TABLE parameter');
        $this->addSql('DROP TABLE vendor');
    }
}
