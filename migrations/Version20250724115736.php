<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250724115736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables for item, order, and product with relationships';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE item (id SERIAL NOT NULL, product_id INT NOT NULL, order_id INT NOT NULL,price NUMERIC(10, 2) NOT NULL, vat NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1F1B251E4584665A ON item (product_id)');
        $this->addSql('CREATE INDEX IDX_1F1B251E8D9F6D38 ON item (order_id)');
        $this->addSql('CREATE TABLE "order" (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, price NUMERIC(10, 2) NOT NULL, vat NUMERIC(10, 2) NOT NULL,test BOOL DEFAULT FALSE, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E8D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item DROP CONSTRAINT FK_1F1B251E4584665A');
        $this->addSql('ALTER TABLE item DROP CONSTRAINT FK_1F1B251E8D9F6D38');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE product');
    }
}
