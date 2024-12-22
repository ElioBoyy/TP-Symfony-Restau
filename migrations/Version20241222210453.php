<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241222210453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_table DROP FOREIGN KEY FK_B5565FE1B83297E7');
        $this->addSql('ALTER TABLE reservation_table DROP FOREIGN KEY FK_B5565FE1ECFF285C');
        $this->addSql('DROP TABLE reservation_table');
        $this->addSql('ALTER TABLE reservation CHANGE commentaire commentary VARCHAR(1024) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation_table (reservation_id INT NOT NULL, table_id INT NOT NULL, INDEX IDX_B5565FE1ECFF285C (table_id), INDEX IDX_B5565FE1B83297E7 (reservation_id), PRIMARY KEY(reservation_id, table_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reservation_table ADD CONSTRAINT FK_B5565FE1B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_table ADD CONSTRAINT FK_B5565FE1ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation CHANGE commentary commentaire VARCHAR(1024) DEFAULT NULL');
    }
}
