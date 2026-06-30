<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260630114800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE daily_entry ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE daily_entry ADD CONSTRAINT FK_AB00FCA4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AB00FCA4A76ED395 ON daily_entry (user_id)');
        $this->addSql('ALTER TABLE user ADD theme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64959027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64959027487 ON user (theme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE daily_entry DROP FOREIGN KEY FK_AB00FCA4A76ED395');
        $this->addSql('DROP INDEX IDX_AB00FCA4A76ED395 ON daily_entry');
        $this->addSql('ALTER TABLE daily_entry DROP user_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64959027487');
        $this->addSql('DROP INDEX IDX_8D93D64959027487 ON user');
        $this->addSql('ALTER TABLE user DROP theme_id');
    }
}
