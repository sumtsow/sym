<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220614125008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parameter ADD av_parameter_id INT NOT NULL');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A97911017C29AE2 FOREIGN KEY (av_parameter_id) REFERENCES av_parameter (id)');
        $this->addSql('CREATE INDEX IDX_2A97911017C29AE2 ON parameter (av_parameter_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parameter DROP FOREIGN KEY FK_2A97911017C29AE2');
        $this->addSql('DROP INDEX IDX_2A97911017C29AE2 ON parameter');
        $this->addSql('ALTER TABLE parameter DROP av_parameter_id');
    }
}
