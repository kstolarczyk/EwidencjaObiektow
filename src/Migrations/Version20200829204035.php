<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200829204035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE obiekty ADD zdjecie VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users_grupy_obiektow DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE users_grupy_obiektow ADD PRIMARY KEY (user_id, grupa_obiektow_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE obiekty DROP zdjecie');
        $this->addSql('ALTER TABLE users_grupy_obiektow DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE users_grupy_obiektow ADD PRIMARY KEY (grupa_obiektow_id, user_id)');
    }
}
