<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109173610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `table` (id INT AUTO_INCREMENT NOT NULL, event_list_id INT NOT NULL, INDEX IDX_F6298F46B8F9BF24 (event_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F46B8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id)');
        $this->addSql('ALTER TABLE checklist CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE event_list CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE event_type CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE expense CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE guest CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE picture CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE rdv CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE tabletab DROP FOREIGN KEY FK_F95DAE60B8F9BF24');
        $this->addSql('DROP INDEX IDX_F95DAE60B8F9BF24 ON tabletab');
        $this->addSql('ALTER TABLE tabletab DROP event_list_id, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY FK_F6298F46B8F9BF24');
        $this->addSql('DROP TABLE `table`');
        $this->addSql('ALTER TABLE checklist CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE event_list CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE event_type CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE expense CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE guest CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE picture CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE property CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE rdv CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tabletab ADD event_list_id INT NOT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tabletab ADD CONSTRAINT FK_F95DAE60B8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F95DAE60B8F9BF24 ON tabletab (event_list_id)');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME DEFAULT NULL');
    }
}
