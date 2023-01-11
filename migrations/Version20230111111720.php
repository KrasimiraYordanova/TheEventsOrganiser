<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230111111720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guest ADD table_tab_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE guest ADD CONSTRAINT FK_ACB79A35D3DE96E0 FOREIGN KEY (table_tab_id) REFERENCES tabletab (id)');
        $this->addSql('CREATE INDEX IDX_ACB79A35D3DE96E0 ON guest (table_tab_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guest DROP FOREIGN KEY FK_ACB79A35D3DE96E0');
        $this->addSql('DROP INDEX IDX_ACB79A35D3DE96E0 ON guest');
        $this->addSql('ALTER TABLE guest DROP table_tab_id');
    }
}
