DELIMITER $

DROP TRIGGER IF EXISTS before_project_insert $
CREATE TRIGGER before_project_insert
BEFORE INSERT
ON project
FOR EACH ROW
BEGIN
 IF NEW.created_at is null THEN
 set NEW.created_at = NOW();
 END IF;
 
 IF NEW.deadline < NEW.created_at THEN
 SIGNAL SQLSTATE '45000'
 SET MESSAGE_TEXT='Deadline must be greater than creation date', MYSQL_ERRNO=3000;
 END IF;
END $

DROP TRIGGER IF EXISTS before_team_insert $
CREATE TRIGGER before_team_insert 
BEFORE INSERT
ON team
FOR EACH ROW
BEGIN
 IF(
	SELECT COUNT(*) FROM project 
	WHERE project_id = NEW.project_id AND class_id IN
	(
		SELECT c.class_id FROM class_member c
		JOIN person p 
		ON p.person_id = c.person_id
	)
 ) = 0     
 THEN
	SIGNAL SQLSTATE '45000'
	SET MESSAGE_TEXT='Team owner is not in the concerned class of the project', MYSQL_ERRNO=3001;
 END IF;
END $

