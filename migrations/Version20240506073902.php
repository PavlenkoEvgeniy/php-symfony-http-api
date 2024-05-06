<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240506073902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author ADD createdAt DATETIME DEFAULT NULL, ADD updatedAt DATETIME DEFAULT NULL, ADD deletedAt DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD createdAt DATETIME DEFAULT NULL, ADD updatedAt DATETIME DEFAULT NULL, ADD deletedAt DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE publisher ADD createdAt DATETIME DEFAULT NULL, ADD updatedAt DATETIME DEFAULT NULL, ADD deletedAt DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author DROP createdAt, DROP updatedAt, DROP deletedAt');
        $this->addSql('ALTER TABLE book DROP createdAt, DROP updatedAt, DROP deletedAt');
        $this->addSql('ALTER TABLE publisher DROP createdAt, DROP updatedAt, DROP deletedAt');
    }
}
