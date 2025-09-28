<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250913102721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add admin user with admin:admin';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO user (username, roles, password) VALUES ('admin', '[\"ROLE_ADMIN\"]', '\$2y\$13\$quDH.8M8bDjV4KADsiSim.XxMVVcc.4AD46ICKC01OzEHIu/bwy0S')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM user WHERE username = 'admin'");
    }
}
