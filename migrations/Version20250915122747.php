<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250915122747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unique constraint from author_to_book table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author_to_book DROP FOREIGN KEY FK_69312DBDF675F31B');
        $this->addSql('DROP INDEX UNIQ_69312DBDF675F31B ON author_to_book');
        $this->addSql('CREATE INDEX IDX_69312DBDF675F31B ON author_to_book (author_id)');
        $this->addSql('ALTER TABLE author_to_book ADD CONSTRAINT FK_69312DBDF675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author_to_book DROP FOREIGN KEY FK_69312DBDF675F31B');
        $this->addSql('DROP INDEX IDX_69312DBDF675F31B ON author_to_book');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_69312DBDF675F31B ON author_to_book (author_id)');
        $this->addSql('ALTER TABLE author_to_book ADD CONSTRAINT FK_69312DBDF675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
    }
}
