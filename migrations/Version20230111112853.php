<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230111112853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tabletab ADD event_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE tabletab ADD CONSTRAINT FK_F95DAE60B8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id)');
        $this->addSql('CREATE INDEX IDX_F95DAE60B8F9BF24 ON tabletab (event_list_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tabletab DROP FOREIGN KEY FK_F95DAE60B8F9BF24');
        $this->addSql('DROP INDEX IDX_F95DAE60B8F9BF24 ON tabletab');
        $this->addSql('ALTER TABLE tabletab DROP event_list_id');
    }
}
