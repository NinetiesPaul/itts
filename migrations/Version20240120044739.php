<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240120044739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO department (id, title) VALUES (1, 'IT')");

        $password = password_hash('admin', PASSWORD_BCRYPT);
        $roles = json_encode( ['ROLE_ADMIN'] );
        $this->addSql("INSERT INTO users (email, roles, password, name, department_id) VALUES ('admin@mail.com', '$roles', '$password', 'admin', 1)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
