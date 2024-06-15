<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612122606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE submission ADD scorer_id INT DEFAULT NULL, ADD scoring_started DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF343B35028 FOREIGN KEY (scorer_id) REFERENCES auto_scorer (id)');
        $this->addSql('CREATE INDEX IDX_DB055AF343B35028 ON submission (scorer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF343B35028');
        $this->addSql('DROP INDEX IDX_DB055AF343B35028 ON submission');
        $this->addSql('ALTER TABLE submission DROP scorer_id, DROP scoring_started');
    }
}
