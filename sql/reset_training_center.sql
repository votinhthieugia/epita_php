DELIMITER $

DROP PROCEDURE IF EXISTS reset_training_center $
CREATE DEFINER=`root`@`localhost` PROCEDURE `reset_training_center`()
BEGIN
	-- Empty tables and reset increments
    SET FOREIGN_KEY_CHECKS=0;
    TRUNCATE TABLE class;
    TRUNCATE TABLE class_member;
    TRUNCATE TABLE document;
    TRUNCATE TABLE person;
    TRUNCATE TABLE project;
    TRUNCATE TABLE team;
    TRUNCATE TABLE team_member;
    SET FOREIGN_KEY_CHECKS=1;
    
    -- Insert data
    INSERT INTO person(person_id, first_name, last_name, address, zip_code, town, email, mobile_phone, password, is_trainer, created_at) VALUES 
    (1, 'Hoang Anh', 'Doan', '17 rue Dalou', '94400', 'Vitry Sur Seine', 'hoanganhdoan2003@yahoo.com', '0785940361', '123456', false, NOW()),
    (2, 'Gustavo', 'Calheiros', '17 rue Dalou', '94400', 'Vilejuif', 'gustavo@gmail.com', '0785940362', '123456', false, NOW()),
    (3, 'Phillipe', 'Paas', '17 Place Italie', '94404', 'Paris', 'philippe@gmail.com', '0785940363', '123456', false, NOW()),
    (4, 'Armel', 'Qoideshi', '17 Dalou', '94400', 'Vitry Sur Seine', 'armel@gmail.com', '0785940364', '123456', false, NOW()),
    (5, 'Favio', 'Tejada', '18 rue Dalou', '94400', 'Vilejuif', 'favio@gmail.com', '0785940365', '123456', false, NOW()),
    (6, 'Adriana', 'Santalla', '19 rue Dalou', '94400', 'Vilejuif', 'adriana@gmail.com', '0785940366', '123456', false, NOW()),
    (7, 'Philippe', 'Laroque', '20 rue Dalou', '94400', 'Vilejuif', 'phillipelaroque@gmail.com', '0785940367', '123456', true, NOW()),
    (8, 'Thomas', 'Broussard', '21 rue Dalou', '94400', 'Vilejuif', 'thomas@gmail.com', '0785940368', '123456', true, NOW());
    
    INSERT INTO class(class_id, name) VALUES
    (1, 'Cuccu'),
    (2, 'Bonjour');
    
    INSERT INTO project(project_id, owner_id, class_id, title, subject, created_at, deadline) VALUES
    (1, 7, 1, 'Algorithm', 'Basic Algorithm', NOW(), Date_ADD(NOW(), INTERVAL 15 DAY)),
    (2, 7, 1, 'C Programming', 'C Advanced', NOW(), Date_ADD(NOW(), INTERVAL 30 DAY)),
    (3, 8, 2, 'Java', 'Advanced JAVA', NOW(), Date_ADD(NOW(), INTERVAL 30 DAY));
    
    INSERT INTO team(team_id, project_id, owner_id, created_at) VALUES 
    (1, 1, 1, NOW()),
    (2, 1, 2, NOW()),
    (3, 1, 3, NOW()),
    (4, 2, 4, NOW());
    
    INSERT INTO team_member(team_id, student_id) VALUES
    (1, 1),
    (1, 2),
	(2, 3),
    (2, 4),
	(3, 5),
    (3, 1),
	(4, 6),
    (4, 3);
    
    INSERT INTO class_member(person_id, class_id) VALUES
    (1, 1),
    (2, 2);
END $