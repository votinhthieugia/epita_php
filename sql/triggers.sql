DELIMITER $

DROP TRIGGER IF EXISTS before_project_insert $
CREATE TRIGGER before_project_insert
BEFORE INSERT
ON project
FOR EACH ROW
BEGIN
 IF DATEDIFF(NEW.deadline, NEW.created_at) >= 0 THEN
 SIGNAL SQLSTATE '45000'
 SET MESSAGE_TEXT='Deadline must be greater than creation date', MYSQL_ERRNO=3000;
 END if;
END $

DROP TRIGGER IF EXISTS before_team_insert $
CREATE TRIGGER before_team_insert
BEFORE INSERT
ON team
FOR EACH ROW
BEGIN
	IF(SELECT COUNT(*) FROM class_member c
	JOIN person p 
	ON p.person_id = c.person_id
	JOIN team t
	ON t.owner_id = c.person_id
    JOIN project proj
    ON proj.owner_id = t.owner_id) = 0     
    THEN
 SIGNAL SQLSTATE '45000'
 SET MESSAGE_TEXT='Team owner is not in the concerned class of the project', MYSQL_ERRNO=3001;
 END if;
END $

