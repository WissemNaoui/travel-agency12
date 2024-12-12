<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241212201100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add flight-specific fields to Booking entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE booking ADD number_of_passengers INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD check_in_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD check_out_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD number_of_guests INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE booking DROP number_of_passengers');
        $this->addSql('ALTER TABLE booking DROP check_in_date');
        $this->addSql('ALTER TABLE booking DROP check_out_date');
        $this->addSql('ALTER TABLE booking DROP number_of_guests');
    }
}
