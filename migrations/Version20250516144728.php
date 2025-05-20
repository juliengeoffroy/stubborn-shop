<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250516144728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des tables sweatshirt, user, messenger_messages avec modifications';
    }

    public function up(Schema $schema): void
    {
        // Création table sweatshirt si elle n'existe pas
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS sweatshirt (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                price DOUBLE PRECISION NOT NULL,
                image VARCHAR(255) DEFAULT NULL,
                is_featured TINYINT(1) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // Création table user si elle n'existe pas
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `user` (
                id INT AUTO_INCREMENT NOT NULL,
                email VARCHAR(180) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(255) DEFAULT NULL,
                delivery_address VARCHAR(255) DEFAULT NULL,
                UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // Création table messenger_messages si elle n'existe pas
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS messenger_messages (
                id BIGINT AUTO_INCREMENT NOT NULL,
                body LONGTEXT NOT NULL,
                headers LONGTEXT NOT NULL,
                queue_name VARCHAR(190) NOT NULL,
                created_at DATETIME NOT NULL,
                available_at DATETIME NOT NULL,
                delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX IDX_75EA56E0FB7336F0 (queue_name),
                INDEX IDX_75EA56E0E3BD61CE (available_at),
                INDEX IDX_75EA56E016BA31DB (delivered_at),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // Modifications
        $this->addSql(<<<'SQL'
            ALTER TABLE sweatshirt CHANGE image image VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE delivery_address delivery_address VARCHAR(255) DEFAULT NULL, CHANGE roles roles JSON NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS sweatshirt');
        $this->addSql('DROP TABLE IF EXISTS `user`');
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
    }
}
