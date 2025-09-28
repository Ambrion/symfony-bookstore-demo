<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250915122748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unique constraint from category_to_book table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_to_book DROP FOREIGN KEY FK_EBD3F28012469DE2');
        $this->addSql('DROP INDEX UNIQ_EBD3F28012469DE2 ON category_to_book');
        $this->addSql('CREATE INDEX IDX_EBD3F28012469DE2 ON category_to_book (category_id)');
        $this->addSql('ALTER TABLE category_to_book ADD CONSTRAINT FK_EBD3F28012469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_to_book DROP FOREIGN KEY FK_EBD3F28012469DE2');
        $this->addSql('DROP INDEX IDX_EBD3F28012469DE2 ON category_to_book');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EBD3F28012469DE2 ON category_to_book (category_id)');
        $this->addSql('ALTER TABLE category_to_book ADD CONSTRAINT FK_EBD3F28012469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }
}
