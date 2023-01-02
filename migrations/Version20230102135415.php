<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230102135415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checklist ADD event_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE checklist ADD CONSTRAINT FK_5C696D2FB8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id)');
        $this->addSql('CREATE INDEX IDX_5C696D2FB8F9BF24 ON checklist (event_list_id)');
        $this->addSql('ALTER TABLE client ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455A76ED395 ON client (user_id)');
        $this->addSql('ALTER TABLE event_list ADD event_type_id INT NOT NULL, ADD client_id INT NOT NULL');
        $this->addSql('ALTER TABLE event_list ADD CONSTRAINT FK_5B03B4B3401B253C FOREIGN KEY (event_type_id) REFERENCES event_type (id)');
        $this->addSql('ALTER TABLE event_list ADD CONSTRAINT FK_5B03B4B319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_5B03B4B3401B253C ON event_list (event_type_id)');
        $this->addSql('CREATE INDEX IDX_5B03B4B319EB6921 ON event_list (client_id)');
        $this->addSql('ALTER TABLE expense ADD event_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6B8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6B8F9BF24 ON expense (event_list_id)');
        $this->addSql('ALTER TABLE guest ADD event_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE guest ADD CONSTRAINT FK_ACB79A35B8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id)');
        $this->addSql('CREATE INDEX IDX_ACB79A35B8F9BF24 ON guest (event_list_id)');
        $this->addSql('ALTER TABLE picture ADD event_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id)');
        $this->addSql('CREATE INDEX IDX_16DB4F89B8F9BF24 ON picture (event_list_id)');
        $this->addSql('ALTER TABLE property ADD event_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE401B253C FOREIGN KEY (event_type_id) REFERENCES event_type (id)');
        $this->addSql('CREATE INDEX IDX_8BF21CDE401B253C ON property (event_type_id)');
        $this->addSql('ALTER TABLE rdv ADD event_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F86B8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id)');
        $this->addSql('CREATE INDEX IDX_10C31F86B8F9BF24 ON rdv (event_list_id)');
        $this->addSql('ALTER TABLE tabletab ADD event_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE tabletab ADD CONSTRAINT FK_F95DAE60B8F9BF24 FOREIGN KEY (event_list_id) REFERENCES event_list (id)');
        $this->addSql('CREATE INDEX IDX_F95DAE60B8F9BF24 ON tabletab (event_list_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checklist DROP FOREIGN KEY FK_5C696D2FB8F9BF24');
        $this->addSql('DROP INDEX IDX_5C696D2FB8F9BF24 ON checklist');
        $this->addSql('ALTER TABLE checklist DROP event_list_id');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6B8F9BF24');
        $this->addSql('DROP INDEX IDX_2D3A8DA6B8F9BF24 ON expense');
        $this->addSql('ALTER TABLE expense DROP event_list_id');
        $this->addSql('ALTER TABLE guest DROP FOREIGN KEY FK_ACB79A35B8F9BF24');
        $this->addSql('DROP INDEX IDX_ACB79A35B8F9BF24 ON guest');
        $this->addSql('ALTER TABLE guest DROP event_list_id');
        $this->addSql('ALTER TABLE tabletab DROP FOREIGN KEY FK_F95DAE60B8F9BF24');
        $this->addSql('DROP INDEX IDX_F95DAE60B8F9BF24 ON tabletab');
        $this->addSql('ALTER TABLE tabletab DROP event_list_id');
        $this->addSql('ALTER TABLE event_list DROP FOREIGN KEY FK_5B03B4B3401B253C');
        $this->addSql('ALTER TABLE event_list DROP FOREIGN KEY FK_5B03B4B319EB6921');
        $this->addSql('DROP INDEX IDX_5B03B4B3401B253C ON event_list');
        $this->addSql('DROP INDEX IDX_5B03B4B319EB6921 ON event_list');
        $this->addSql('ALTER TABLE event_list DROP event_type_id, DROP client_id');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('DROP INDEX UNIQ_C7440455A76ED395 ON client');
        $this->addSql('ALTER TABLE client DROP user_id');
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F86B8F9BF24');
        $this->addSql('DROP INDEX IDX_10C31F86B8F9BF24 ON rdv');
        $this->addSql('ALTER TABLE rdv DROP event_list_id');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE401B253C');
        $this->addSql('DROP INDEX IDX_8BF21CDE401B253C ON property');
        $this->addSql('ALTER TABLE property DROP event_type_id');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B8F9BF24');
        $this->addSql('DROP INDEX IDX_16DB4F89B8F9BF24 ON picture');
        $this->addSql('ALTER TABLE picture DROP event_list_id');
    }
}
