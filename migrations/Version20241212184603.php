<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212184603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set default value for max_guests in hotel table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE91F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE3243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        
        // Update existing records
        $this->addSql('UPDATE hotel SET max_guests = 4 WHERE max_guests = 0 OR max_guests IS NULL');
        
        // Set default value for future records
        $this->addSql('ALTER TABLE hotel MODIFY max_guests INT NOT NULL DEFAULT 4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE91F478C5');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE3243BB18');
        
        $this->addSql('ALTER TABLE hotel MODIFY max_guests INT NOT NULL DEFAULT 0');
    }
}
