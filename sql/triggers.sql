DELIMITER $

DROP TRIGGER IF EXISTS before_project_insert $
CREATE TRIGGER before_project_insert
BEFORE INSERT
ON project
FOR EACH ROW
BEGIN
 CALL validate_project_deadline(NEW.created_at, NEW.deadline);
END $

DROP TRIGGER IF EXISTS before_project_update $
CREATE TRIGGER before_project_update
BEFORE UPDATE
ON project
FOR EACH ROW
BEGIN
 CALL validate_project_deadline(NEW.created_at, NEW.deadline);
END $

#---------------

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

DROP TRIGGER IF EXISTS before_add_team_member $
CREATE TRIGGER before_add_team_member
BEFORE INSERT
ON team_member
FOR EACH ROW
BEGIN
IF(
	SELECT COUNT(*) FROM class_member
    WHERE class_member = NEW.student_id AND
    class_id = 
    (
		SELECT class_id FROM project  
		WHERE project_id = 
		(
			SELECT t.project_id FROM team t
			WHERE NEW.team_id = t.team_id
		)
	)
 ) = 0     
 THEN
	SIGNAL SQLSTATE '45000'
	SET MESSAGE_TEXT='The member is not part of the project\'s class', MYSQL_ERRNO=3002;
 END IF;
END $