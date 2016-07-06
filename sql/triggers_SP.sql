DELIMITER $

DROP PROCEDURE IF EXISTS validate_project_deadline $
CREATE DEFINER=`root`@`localhost` PROCEDURE validate_project_deadline
(IN created_at VARCHAR(30), IN deadline VARCHAR(30))
READS SQL DATA
BEGIN
IF created_at is null THEN
 set created_at = NOW();
 END IF;
 
 IF deadline < created_at THEN
 SIGNAL SQLSTATE '45000'
 SET MESSAGE_TEXT='Deadline must be greater than creation date', MYSQL_ERRNO=3000;
 END IF;

END $
