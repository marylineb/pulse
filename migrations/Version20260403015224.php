<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260403015224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE chat_room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, alias_a VARCHAR(50) NOT NULL, alias_b VARCHAR(50) NOT NULL, is_active BOOLEAN NOT NULL, created_at DATETIME NOT NULL, crush_id INTEGER NOT NULL, user_a_id INTEGER NOT NULL, user_b_id INTEGER NOT NULL, CONSTRAINT FK_D403CCDAC2BE7C90 FOREIGN KEY (crush_id) REFERENCES crush (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D403CCDA415F1F91 FOREIGN KEY (user_a_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D403CCDA53EAB07F FOREIGN KEY (user_b_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D403CCDAC2BE7C90 ON chat_room (crush_id)');
        $this->addSql('CREATE INDEX IDX_D403CCDA415F1F91 ON chat_room (user_a_id)');
        $this->addSql('CREATE INDEX IDX_D403CCDA53EAB07F ON chat_room (user_b_id)');
        $this->addSql('CREATE TABLE crush (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, status VARCHAR(40) NOT NULL, is_mutual BOOLEAN NOT NULL, created_at DATETIME NOT NULL, sender_id INTEGER NOT NULL, target_id INTEGER NOT NULL, CONSTRAINT FK_CCC7FB73F624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CCC7FB73158E0B66 FOREIGN KEY (target_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_CCC7FB73F624B39D ON crush (sender_id)');
        $this->addSql('CREATE INDEX IDX_CCC7FB73158E0B66 ON crush (target_id)');
        $this->addSql('CREATE TABLE message (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, content CLOB NOT NULL, is_flagged BOOLEAN NOT NULL, created_at DATETIME NOT NULL, room_id INTEGER NOT NULL, author_id INTEGER NOT NULL, CONSTRAINT FK_B6BD307F54177093 FOREIGN KEY (room_id) REFERENCES chat_room (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B6BD307FF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B6BD307F54177093 ON message (room_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FF675F31B ON message (author_id)');
        $this->addSql('CREATE TABLE profile (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pseudo VARCHAR(50) NOT NULL, firstname VARCHAR(50) DEFAULT NULL, age INTEGER DEFAULT NULL, bio CLOB DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, vibe VARCHAR(100) DEFAULT NULL, interests CLOB DEFAULT NULL, created_at DATETIME NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_8157AA0FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8157AA0FA76ED395 ON profile (user_id)');
        $this->addSql('CREATE TABLE report (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reason VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, room_id INTEGER NOT NULL, reporter_id INTEGER NOT NULL, message_id INTEGER DEFAULT NULL, CONSTRAINT FK_C42F778454177093 FOREIGN KEY (room_id) REFERENCES chat_room (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C42F7784E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C42F7784537A1329 FOREIGN KEY (message_id) REFERENCES message (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C42F778454177093 ON report (room_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784E1CFE6F5 ON report (reporter_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784537A1329 ON report (message_id)');
        $this->addSql('CREATE TABLE reveal_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, accepted_by_requester BOOLEAN NOT NULL, accepted_by_other BOOLEAN NOT NULL, revealed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, room_id INTEGER NOT NULL, requested_by_id INTEGER NOT NULL, CONSTRAINT FK_8D1DD57954177093 FOREIGN KEY (room_id) REFERENCES chat_room (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8D1DD5794DA1E751 FOREIGN KEY (requested_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8D1DD57954177093 ON reveal_request (room_id)');
        $this->addSql('CREATE INDEX IDX_8D1DD5794DA1E751 ON reveal_request (requested_by_id)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE chat_room');
        $this->addSql('DROP TABLE crush');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE reveal_request');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
