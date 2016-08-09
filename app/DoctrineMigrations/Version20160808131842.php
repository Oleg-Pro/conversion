<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160808131842 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE clients (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, surname VARCHAR(20) NOT NULL, phone VARCHAR(20) NOT NULL, status VARCHAR(255) NOT NULL, datetime DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->connection->exec("DROP PROCEDURE IF EXISTS clients_convertion;
    CREATE PROCEDURE clients_convertion (daysInPeriod INT)
    BEGIN
	  SET @lastDate = (SELECT DATE(MAX(datetime)) FROM clients);
	  SET @currentStart = (SELECT DATE(MIN(datetime)) FROM clients);
	  SET @query = '';
	  REPEAT
		SET @currentEnd = DATE_ADD(@currentStart, INTERVAL daysInPeriod DAY);
		SET @query = CONCAT(@query,
						   if(@query = '', '', ' UNION ALL '),
						   CONCAT('SELECT \"', @currentStart, ' - ', DATE_ADD(@currentEnd, INTERVAL -1 DAY), '\" AS period, '),
						   '(SELECT COUNT(*) FROM clients WHERE status =',
						   '\"', 'registered', '\"',
						   ' AND DATE(clients.datetime) >= \"', @currentStart,
						   '\" AND DATE(clients.datetime) <  \"', @currentEnd,
						   '\") AS registered_clients_number, (',
						   'SELECT COUNT(*) FROM clients ',
						   'WHERE DATE(clients.datetime) >= \"', @currentStart,
  						   '\" AND DATE(clients.datetime) <  \"', @currentEnd, '\") AS clients_number' 						   
						   );
		SET @currentStart = @currentEnd;
	  UNTIL @currentStart > @lastDate  END REPEAT;	  
	  PREPARE stmt3 FROM @query;
      EXECUTE stmt3;
      DEALLOCATE PREPARE stmt3;
    END;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP PROCEDURE IF EXISTS clients_convertion;');
    }
}
