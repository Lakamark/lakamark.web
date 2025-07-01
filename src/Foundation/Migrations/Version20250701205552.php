<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250701205552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE content ADD image_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content ADD CONSTRAINT FK_FEC530A93DA5256D FOREIGN KEY (image_id) REFERENCES attachment (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FEC530A93DA5256D ON content (image_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE content DROP FOREIGN KEY FK_FEC530A93DA5256D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_FEC530A93DA5256D ON content
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content DROP image_id
        SQL);
    }
}
