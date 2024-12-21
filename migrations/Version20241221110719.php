<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241221110719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT NOT NULL, name VARCHAR(50) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_7D053A93B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT NOT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_DD5A5B7DB1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plat (id INT AUTO_INCREMENT NOT NULL, menu_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_2038A207CCD7E912 (menu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, nb_personne INT NOT NULL, commentary VARCHAR(1024) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_42C84955B1E7706E (restaurant_id), INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(15) NOT NULL, country VARCHAR(50) NOT NULL, address VARCHAR(255) NOT NULL, zip_code VARCHAR(30) NOT NULL, horaires JSON NOT NULL, INDEX IDX_EB95123F7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `table` (id INT AUTO_INCREMENT NOT NULL, plan_id INT NOT NULL, position_x INT NOT NULL, position_y INT NOT NULL, is_active TINYINT(1) NOT NULL, nb_personne_max INT NOT NULL, INDEX IDX_F6298F46E899029B (plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE table_reservation (table_id INT NOT NULL, reservation_id INT NOT NULL, INDEX IDX_7196BAE8ECFF285C (table_id), INDEX IDX_7196BAE8B83297E7 (reservation_id), PRIMARY KEY(table_id, reservation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(15) NOT NULL, country VARCHAR(50) NOT NULL, address VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallpoint (id INT AUTO_INCREMENT NOT NULL, plan_id INT NOT NULL, position_x INT NOT NULL, position_y INT NOT NULL, INDEX IDX_2C2DF71EE899029B (plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7DB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE plat ADD CONSTRAINT FK_2038A207CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F46E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('ALTER TABLE table_reservation ADD CONSTRAINT FK_7196BAE8ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE table_reservation ADD CONSTRAINT FK_7196BAE8B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wallpoint ADD CONSTRAINT FK_2C2DF71EE899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93B1E7706E');
        $this->addSql('ALTER TABLE plan DROP FOREIGN KEY FK_DD5A5B7DB1E7706E');
        $this->addSql('ALTER TABLE plat DROP FOREIGN KEY FK_2038A207CCD7E912');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955B1E7706E');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE restaurant DROP FOREIGN KEY FK_EB95123F7E3C61F9');
        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY FK_F6298F46E899029B');
        $this->addSql('ALTER TABLE table_reservation DROP FOREIGN KEY FK_7196BAE8ECFF285C');
        $this->addSql('ALTER TABLE table_reservation DROP FOREIGN KEY FK_7196BAE8B83297E7');
        $this->addSql('ALTER TABLE wallpoint DROP FOREIGN KEY FK_2C2DF71EE899029B');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE plan');
        $this->addSql('DROP TABLE plat');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE `table`');
        $this->addSql('DROP TABLE table_reservation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE wallpoint');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
