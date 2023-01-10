<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109113302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_property (id INT AUTO_INCREMENT NOT NULL, property_id INT NOT NULL, event_list_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_CD1EF50E549213EC (property_id), INDEX IDX_CD1EF50EB8F9BF24 (event_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_property ADD CONSTRAINT FK_CD1EF50E549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE event_property ADD CONSTRAINT FK_CD1EF50EB8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id)');
        $this->addSql('ALTER TABLE rdv ADD name VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_property DROP FOREIGN KEY FK_CD1EF50E549213EC');
        $this->addSql('ALTER TABLE event_property DROP FOREIGN KEY FK_CD1EF50EB8F9BF24');
        $this->addSql('DROP TABLE event_property');
        $this->addSql('ALTER TABLE rdv DROP name, CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP image');
    }
}
