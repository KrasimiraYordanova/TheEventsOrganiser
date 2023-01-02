<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230102173032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guest ADD tabletab_id INT DEFAULT NULL, DROP table_namebor');
        $this->addSql('ALTER TABLE guest ADD CONSTRAINT FK_ACB79A35BC8B2C7 FOREIGN KEY (tabletab_id) REFERENCES tabletab (id)');
        $this->addSql('CREATE INDEX IDX_ACB79A35BC8B2C7 ON guest (tabletab_id)');
        $this->addSql('ALTER TABLE picture ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE guest DROP FOREIGN KEY FK_ACB79A35BC8B2C7');
        $this->addSql('DROP INDEX IDX_ACB79A35BC8B2C7 ON guest');
        $this->addSql('ALTER TABLE guest ADD table_namebor VARCHAR(255) DEFAULT NULL, DROP tabletab_id');
    }
}
