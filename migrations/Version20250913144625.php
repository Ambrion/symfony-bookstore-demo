<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250913144625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create database tables book, author and relations';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, isbn VARCHAR(20) NOT NULL, page_count INT NOT NULL, published_date DATE DEFAULT NULL, image VARCHAR(512) DEFAULT NULL, short_description VARCHAR(512) DEFAULT NULL, long_description LONGTEXT DEFAULT NULL, status VARCHAR(24) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE author_to_book (book_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_69312DBD16A2B381 (book_id), UNIQUE INDEX UNIQ_69312DBDF675F31B (author_id), PRIMARY KEY(book_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_to_book (book_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_EBD3F28016A2B381 (book_id), UNIQUE INDEX UNIQ_EBD3F28012469DE2 (category_id), PRIMARY KEY(book_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE author_to_book ADD CONSTRAINT FK_69312DBD16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE author_to_book ADD CONSTRAINT FK_69312DBDF675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('ALTER TABLE category_to_book ADD CONSTRAINT FK_EBD3F28016A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE category_to_book ADD CONSTRAINT FK_EBD3F28012469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author_to_book DROP FOREIGN KEY FK_69312DBD16A2B381');
        $this->addSql('ALTER TABLE author_to_book DROP FOREIGN KEY FK_69312DBDF675F31B');
        $this->addSql('ALTER TABLE category_to_book DROP FOREIGN KEY FK_EBD3F28016A2B381');
        $this->addSql('ALTER TABLE category_to_book DROP FOREIGN KEY FK_EBD3F28012469DE2');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE author_to_book');
        $this->addSql('DROP TABLE category_to_book');
    }
}
