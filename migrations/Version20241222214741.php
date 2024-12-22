<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241222214741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP date_time');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('CREATE INDEX IDX_42C84955ECFF285C ON reservation (table_id)');
        $this->addSql('CREATE INDEX IDX_42C84955E899029B ON reservation (plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955ECFF285C');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955E899029B');
        $this->addSql('DROP INDEX IDX_42C84955ECFF285C ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955E899029B ON reservation');
        $this->addSql('ALTER TABLE reservation ADD date_time DATETIME NOT NULL');
    }
}
