DROP PROCEDURE IF EXISTS clients_convertion;
    delimiter //
    CREATE PROCEDURE clients_convertion (daysInPeriod INT)
    BEGIN
	  SET @lastDate = (SELECT DATE(MAX(datetime)) FROM clients);
	  SET @currentStart = (SELECT DATE(MIN(datetime)) FROM clients);
	  SET @query = '';
	  REPEAT
		SET @currentEnd = DATE_ADD(@currentStart, INTERVAL daysInPeriod DAY);
		SET @query = CONCAT(@query,
						   if(@query = '', '', ' UNION ALL '),
						   CONCAT('SELECT "', @currentStart, ' - ', DATE_ADD(@currentEnd, INTERVAL -1 DAY), '" AS period, '),
						   '(SELECT COUNT(*) FROM clients WHERE status =',
						   '"', 'registered', '"',
						   ' AND DATE(clients.datetime) >= "', @currentStart,
						   '" AND DATE(clients.datetime) <  "', @currentEnd,
						   '") AS registered_clients_number, (',
						   'SELECT COUNT(*) FROM clients ',
						   'WHERE DATE(clients.datetime) >= "', @currentStart,
  						   '" AND DATE(clients.datetime) <  "', @currentEnd, '") AS clients_number'
						   );
		SET @currentStart = @currentEnd;
	  UNTIL @currentStart > @lastDate  END REPEAT;
	  PREPARE stmt3 FROM @query;
      EXECUTE stmt3;
      DEALLOCATE PREPARE stmt3;
    END//
    delimiter ;