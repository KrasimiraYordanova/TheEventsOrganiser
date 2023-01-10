<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109184314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY FK_F6298F46B8F9BF24');
        $this->addSql('DROP TABLE `table`');
        $this->addSql('ALTER TABLE event_property ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE guest DROP FOREIGN KEY FK_ACB79A35BC8B2C7');
        $this->addSql('DROP INDEX IDX_ACB79A35BC8B2C7 ON guest');
        $this->addSql('ALTER TABLE guest DROP tabletab_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `table` (id INT AUTO_INCREMENT NOT NULL, event_list_id INT NOT NULL, INDEX IDX_F6298F46B8F9BF24 (event_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F46B8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE event_property DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE guest ADD tabletab_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE guest ADD CONSTRAINT FK_ACB79A35BC8B2C7 FOREIGN KEY (tabletab_id) REFERENCES tabletab (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_ACB79A35BC8B2C7 ON guest (tabletab_id)');
    }
}
