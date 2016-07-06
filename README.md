# TRAINING CENTER DOCUMENTATION


Hoang Anh Doan
-
Gustavo Calheiros
-

Code: https://github.com/votinhthieugia/epita_php

Features Data Base:
-
**epita_php/sql/triggers.sql:**

- **before_project_insert/before_project_update:** Checks if the project deadline is greater than creation date

- **before_team_insert:** Checks if the Team owner is in the concerned class of the project

- **before_add_team_member:** Checks if the team member is part of the project\'s class

**epita_php/sql/reset_training_center.sql:**

- **before_add_team_member:** Checks if the team member is part of the project's class
 
**file epita_php/sql/triggers_SP.sql:**

- **validate_project_deadline:** Stored procedure to avoid duplication of trigger for insert and update
- 

Features PHP: Student
-

* Create Team
* Update Team's Summary
* See his teams and its attributes
* Add and remove members from his team
* Create Team

Features PHP: Trainer
-

* Create Project
* View all projects
* Edit his own projects (title, deadline and subject)
* Add and remove members from his team
 
Features PHP: Code structure
-

- **RestAPI:** Developed to simplify the callings to the webservices
- **bootstrap**: Used to make the project more beautiful :)
