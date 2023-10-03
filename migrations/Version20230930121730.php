<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230930121730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion CHANGE trick_id_id trick_id_id INT NOT NULL, CHANGE user_id userId INT NOT NULL');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F64B64DCC FOREIGN KEY (userId) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90F64B64DCC ON discussion (userId)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F64B64DCC');
        $this->addSql('DROP INDEX IDX_C0B9F90F64B64DCC ON discussion');
        $this->addSql('ALTER TABLE discussion CHANGE trick_id_id trick_id_id INT DEFAULT NULL, CHANGE userId user_id INT NOT NULL');
    }
}
