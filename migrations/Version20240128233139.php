<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240128233139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE call_notes (id INT AUTO_INCREMENT NOT NULL, call_id_id INT NOT NULL, sent_by_id INT NOT NULL, text LONGTEXT DEFAULT NULL, INDEX IDX_63F13A341DAE02EF (call_id_id), INDEX IDX_63F13A34A45BB98C (sent_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_notes ADD CONSTRAINT FK_63F13A341DAE02EF FOREIGN KEY (call_id_id) REFERENCES calls (id)');
        $this->addSql('ALTER TABLE call_notes ADD CONSTRAINT FK_63F13A34A45BB98C FOREIGN KEY (sent_by_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE call_notes DROP FOREIGN KEY FK_63F13A341DAE02EF');
        $this->addSql('ALTER TABLE call_notes DROP FOREIGN KEY FK_63F13A34A45BB98C');
        $this->addSql('DROP TABLE call_notes');
    }
}
