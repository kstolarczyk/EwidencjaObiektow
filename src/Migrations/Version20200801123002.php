<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200801123002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users_grupy_obiektow (grupa_obiektow_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_81D7E2BB931D4186 (grupa_obiektow_id), INDEX IDX_81D7E2BBA76ED395 (user_id), PRIMARY KEY(grupa_obiektow_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_grupy_obiektow ADD CONSTRAINT FK_81D7E2BB931D4186 FOREIGN KEY (grupa_obiektow_id) REFERENCES grupy_obiektow (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_grupy_obiektow ADD CONSTRAINT FK_81D7E2BBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE grupy_obiektow_typy_parametrow DROP FOREIGN KEY FK_91BCF93A1BC3469B');
        $this->addSql('ALTER TABLE grupy_obiektow_typy_parametrow DROP FOREIGN KEY FK_91BCF93A931D4186');
        $this->addSql('ALTER TABLE grupy_obiektow_typy_parametrow ADD CONSTRAINT FK_91BCF93A1BC3469B FOREIGN KEY (typ_parametru_id) REFERENCES typy_parametrow (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE grupy_obiektow_typy_parametrow ADD CONSTRAINT FK_91BCF93A931D4186 FOREIGN KEY (grupa_obiektow_id) REFERENCES grupy_obiektow (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE users_grupy_obiektow');
        $this->addSql('ALTER TABLE grupy_obiektow_typy_parametrow DROP FOREIGN KEY FK_91BCF93A931D4186');
        $this->addSql('ALTER TABLE grupy_obiektow_typy_parametrow DROP FOREIGN KEY FK_91BCF93A1BC3469B');
        $this->addSql('ALTER TABLE grupy_obiektow_typy_parametrow ADD CONSTRAINT FK_91BCF93A931D4186 FOREIGN KEY (grupa_obiektow_id) REFERENCES grupy_obiektow (id)');
        $this->addSql('ALTER TABLE grupy_obiektow_typy_parametrow ADD CONSTRAINT FK_91BCF93A1BC3469B FOREIGN KEY (typ_parametru_id) REFERENCES typy_parametrow (id)');
    }
}
