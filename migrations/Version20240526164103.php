<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240526164103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auto_scorer (id INT AUTO_INCREMENT NOT NULL, last_submission_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, api_key VARCHAR(255) NOT NULL, last_check DATETIME DEFAULT NULL, last_result DATETIME DEFAULT NULL, status VARCHAR(100) NOT NULL, last_ip VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_5A31A9338DF22AA4 (last_submission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auto_scorer ADD CONSTRAINT FK_5A31A9338DF22AA4 FOREIGN KEY (last_submission_id) REFERENCES submission (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auto_scorer DROP FOREIGN KEY FK_5A31A9338DF22AA4');
        $this->addSql('DROP TABLE auto_scorer');
    }
}
