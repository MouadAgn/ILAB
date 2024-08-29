<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240829162526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F84487F45686');
        $this->addSql('DROP INDEX fk_fe38f84487f45686 ON appointment');
        $this->addSql('CREATE INDEX IDX_FE38F844FE2B23CD ON appointment (type_of_test_id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F84487F45686 FOREIGN KEY (type_of_test_id) REFERENCES test (id)');
        $this->addSql('ALTER TABLE patient CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE deleted_at deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844FE2B23CD');
        $this->addSql('DROP INDEX idx_fe38f844fe2b23cd ON appointment');
        $this->addSql('CREATE INDEX FK_FE38F84487F45686 ON appointment (type_of_test_id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844FE2B23CD FOREIGN KEY (type_of_test_id) REFERENCES test (id)');
        $this->addSql('ALTER TABLE patient CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'	(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'	(DC2Type:datetime_immutable)\', CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
    }
}
