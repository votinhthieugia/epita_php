<?php

require_once("HttpResource.php");
require_once("DemoDB.php");

#require_once(__DIR__ . '/../php_console/src/PhpConsole/__autoload.php');
#$handler = PhpConsole\Handler::getInstance();
#$handler->start();

class PersonNotInTeamResource extends HttpResource {
  /** Person id */
  protected $id;

  /** Initialize $id. Send 400 if id missing or not positive integer */
  public function init() {
	  	
    if (isset($_GET["project_id"]) && isset($_GET["class_id"])) {
      if (is_numeric($_GET["project_id"])) {
        $this->project_id = 0 + $_GET["project_id"];
        $this->class_id = 0 + $_GET["class_id"];
        if (!is_int($this->project_id) || $this->project_id <= 0) {
          $this->exit_error(400, "idNotPositiveInteger");
        }
      }
      else {
        $this->exit_error(400, "idNotPositiveInteger");
      }
    }
    else {
      $this->exit_error(400, "missing params");
    }
  }

  protected function do_get(){
    // Call the parent
    parent::do_get();
    try {
      $db = DemoDB::getConnection();
		$sql = 
		"SELECT person_id, first_name, last_name from person
			WHERE person_id = 
			(
				SELECT person_id FROM class_member
				WHERE class_id = :class_id AND person_id NOT IN
				(
					SELECT student_id FROM team_member
					WHERE team_id IN
					(
						SELECT team_id FROM team
						WHERE project_id = :project_id
					)
					GROUP BY student_id
				)
			)";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(":project_id", $this->project_id);
		$stmt->bindValue(":class_id", $this->class_id);
      $ok = $stmt->execute();
      if ($ok) {
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($row != null) {
          $this->statusCode = 200;
          // Produce utf8 encoded json
          $this->headers[] = "Content-type: text/json; charset=utf-8";
          $this->body = json_encode($row);
        }
        else {
          $this->exit_error(404);
        }
      }
      else {
        $this->exit_error(500, print_r($db->errorInfo(), true));
      }
    }
    catch (PDOException $e) {
      $this->exit_error(500, $e->getMessage());
    }
  }

}

// Simply run the resource
PersonNotInTeamResource::run();
?>
