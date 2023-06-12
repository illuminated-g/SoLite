<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230611010008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge_run (id INT AUTO_INCREMENT NOT NULL, challenge_id INT DEFAULT NULL, start DATETIME DEFAULT NULL, finish DATETIME DEFAULT NULL, info LONGTEXT NOT NULL, INDEX IDX_493D09CC98A21AC6 (challenge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE submission (id INT AUTO_INCREMENT NOT NULL, participant_id INT DEFAULT NULL, run_id INT DEFAULT NULL, approved_by_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, submitted DATETIME NOT NULL, score DOUBLE PRECISION NOT NULL, approved TINYINT(1) NOT NULL, approved_on DATETIME DEFAULT NULL, INDEX IDX_DB055AF39D1C3019 (participant_id), INDEX IDX_DB055AF384E3FEC4 (run_id), INDEX IDX_DB055AF32D234F6A (approved_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, country VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, ni_email VARCHAR(255) DEFAULT NULL, champion TINYINT(1) NOT NULL, ni_employee TINYINT(1) NOT NULL, partner TINYINT(1) NOT NULL, clad TINYINT(1) NOT NULL, cld TINYINT(1) NOT NULL, cla TINYINT(1) NOT NULL, ctd TINYINT(1) NOT NULL, cta TINYINT(1) NOT NULL, cled TINYINT(1) NOT NULL, verified TINYINT(1) NOT NULL, verified_on DATETIME DEFAULT NULL, verification VARCHAR(255) DEFAULT NULL, change_pass TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE challenge_run ADD CONSTRAINT FK_493D09CC98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF39D1C3019 FOREIGN KEY (participant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF384E3FEC4 FOREIGN KEY (run_id) REFERENCES challenge_run (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF32D234F6A FOREIGN KEY (approved_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge_run DROP FOREIGN KEY FK_493D09CC98A21AC6');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF39D1C3019');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF384E3FEC4');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF32D234F6A');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE challenge_run');
        $this->addSql('DROP TABLE submission');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
