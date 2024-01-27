<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240122022656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment ADD has_parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D58353DF583F FOREIGN KEY (has_parent_id) REFERENCES equipment (id)');
        $this->addSql('CREATE INDEX IDX_D338D58353DF583F ON equipment (has_parent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D58353DF583F');
        $this->addSql('DROP INDEX IDX_D338D58353DF583F ON equipment');
        $this->addSql('ALTER TABLE equipment DROP has_parent_id');
    }
}
