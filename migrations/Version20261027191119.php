<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261027191119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute chat_room.is_revealed';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chat_room ADD COLUMN is_revealed BOOLEAN DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__chat_room AS SELECT id, created_at, user_one_id, user_two_id FROM chat_room');
        $this->addSql('DROP TABLE chat_room');
        $this->addSql('CREATE TABLE chat_room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, user_one_id INTEGER NOT NULL, user_two_id INTEGER NOT NULL, CONSTRAINT FK_D403CCDA9EC8D52E FOREIGN KEY (user_one_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D403CCDAF59432E1 FOREIGN KEY (user_two_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO chat_room (id, created_at, user_one_id, user_two_id) SELECT id, created_at, user_one_id, user_two_id FROM __temp__chat_room');
        $this->addSql('DROP TABLE __temp__chat_room');
        $this->addSql('CREATE INDEX IDX_D403CCDA9EC8D52E ON chat_room (user_one_id)');
        $this->addSql('CREATE INDEX IDX_D403CCDAF59432E1 ON chat_room (user_two_id)');
    }
}
