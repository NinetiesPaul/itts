<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201012820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calls ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE calls ADD CONSTRAINT FK_DAA35C8F6BF700BD FOREIGN KEY (status_id) REFERENCES status_type (id)');
        $this->addSql('CREATE INDEX IDX_DAA35C8F6BF700BD ON calls (status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calls DROP FOREIGN KEY FK_DAA35C8F6BF700BD');
        $this->addSql('DROP INDEX IDX_DAA35C8F6BF700BD ON calls');
        $this->addSql('ALTER TABLE calls DROP status_id');
    }
}
