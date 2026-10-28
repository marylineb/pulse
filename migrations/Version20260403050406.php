<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260403050406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__chat_room AS SELECT id, created_at FROM chat_room');
        $this->addSql('DROP TABLE chat_room');
        $this->addSql('CREATE TABLE chat_room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, user_one_id INTEGER NOT NULL, user_two_id INTEGER NOT NULL, CONSTRAINT FK_D403CCDA9EC8D52E FOREIGN KEY (user_one_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D403CCDAF59432E1 FOREIGN KEY (user_two_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO chat_room (id, created_at) SELECT id, created_at FROM __temp__chat_room');
        $this->addSql('DROP TABLE __temp__chat_room');
        $this->addSql('CREATE INDEX IDX_D403CCDA9EC8D52E ON chat_room (user_one_id)');
        $this->addSql('CREATE INDEX IDX_D403CCDAF59432E1 ON chat_room (user_two_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__profile AS SELECT id, pseudo, firstname, age, bio, avatar, vibe, interests, user_id FROM profile');
        $this->addSql('DROP TABLE profile');
        $this->addSql('CREATE TABLE profile (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pseudo VARCHAR(50) NOT NULL, firstname VARCHAR(50) DEFAULT NULL, age INTEGER DEFAULT NULL, bio CLOB DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, vibe VARCHAR(50) DEFAULT NULL, interests CLOB DEFAULT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_8157AA0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO profile (id, pseudo, firstname, age, bio, avatar, vibe, interests, user_id) SELECT id, pseudo, firstname, age, bio, avatar, vibe, interests, user_id FROM __temp__profile');
        $this->addSql('DROP TABLE __temp__profile');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8157AA0FA76ED395 ON profile (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__chat_room AS SELECT id, created_at FROM chat_room');
        $this->addSql('DROP TABLE chat_room');
        $this->addSql('CREATE TABLE chat_room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, alias_a VARCHAR(50) NOT NULL, alias_b VARCHAR(50) NOT NULL, is_active BOOLEAN NOT NULL, crush_id INTEGER NOT NULL, user_a_id INTEGER NOT NULL, user_b_id INTEGER NOT NULL, CONSTRAINT FK_D403CCDAC2BE7C90 FOREIGN KEY (crush_id) REFERENCES crush (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D403CCDA415F1F91 FOREIGN KEY (user_a_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D403CCDA53EAB07F FOREIGN KEY (user_b_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO chat_room (id, created_at) SELECT id, created_at FROM __temp__chat_room');
        $this->addSql('DROP TABLE __temp__chat_room');
        $this->addSql('CREATE INDEX IDX_D403CCDA53EAB07F ON chat_room (user_b_id)');
        $this->addSql('CREATE INDEX IDX_D403CCDA415F1F91 ON chat_room (user_a_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D403CCDAC2BE7C90 ON chat_room (crush_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__profile AS SELECT id, pseudo, firstname, age, bio, avatar, vibe, interests, user_id FROM profile');
        $this->addSql('DROP TABLE profile');
        $this->addSql('CREATE TABLE profile (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pseudo VARCHAR(50) NOT NULL, firstname VARCHAR(50) DEFAULT NULL, age INTEGER DEFAULT NULL, bio CLOB DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, vibe VARCHAR(100) DEFAULT NULL, interests CLOB DEFAULT NULL, user_id INTEGER NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_8157AA0FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO profile (id, pseudo, firstname, age, bio, avatar, vibe, interests, user_id) SELECT id, pseudo, firstname, age, bio, avatar, vibe, interests, user_id FROM __temp__profile');
        $this->addSql('DROP TABLE __temp__profile');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8157AA0FA76ED395 ON profile (user_id)');
    }
}
