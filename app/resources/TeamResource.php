<?php
require_once("DemoDB.php");
require_once("HttpResource.php");

//DEBUG
//require_once(__DIR__ . '/../php_console/src/PhpConsole/__autoload.php');
//$handler = PhpConsole\Handler::getInstance();
//$handler->start();

class TeamResource extends HttpResource {
  protected $id;

  /** Initialize $id. Send 400 if id missing or not positive integer */
  public function init() {
    if (isset($_GET["id"])) {
      if (is_numeric($_GET["id"])) {
        $this->id = 0 + $_GET["id"]; // transformer en numerique
        if (!is_int($this->id) || $this->id <= 0) {
          $this->exit_error(400, "idNotPositiveInteger");
        }
      }
      //else {
       // $this->exit_error(400, "idNotPositiveInteger");
      //}
    }
    else if (isset($_GET["project_id"])) {
		if (is_numeric($_GET["project_id"])) {
        $this->project_id = 0 + $_GET["project_id"]; // transformer en numerique
        if (!is_int($this->project_id) || $this->project_id <= 0) {
          $this->exit_error(400, "project_idproject_id");
        }
      }
	}
    else {
      $this->id = -1;
    }
  }

  protected function do_get() {
    // Call the parent
    parent::do_get();
    try {
      $db = DemoDB::getConnection();
      if (isset($this->project_id)) {
		  $sql = "SELECT team_id, project_id, owner_id, summary, created_at FROM team WHERE project_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $this->project_id);
	  }
      else 
      if ($this->id == -1) {
        $sql = "SELECT team_id, project_id, owner_id, summary, created_at FROM team";
        $stmt = $db->prepare($sql);
      } else {
        $sql = "SELECT team_id, project_id, owner_id, summary, created_at FROM team WHERE team_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $this->id);
      }
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

  protected function do_post() {
    parent::do_post();
    try {
      $db = DemoDB::getConnection();
      $sql = "INSERT INTO team(owner_id, project_id, summary, created_at) VALUES(?, ?, ?, ?)";
      $stmt = $db->prepare($sql);
      $data = array($_POST["ownerId"], $_POST["projectId"], $_POST["summary"], date('Y-m-d H:i:s'));
      $ok = $stmt->execute($data);
      if ($ok) {
      } else {
        $this->exit_error(500);
      }
    } catch (PDOException $e) {
      $this->exit_error(500, $e->getMessage());
    }
  }

  protected function do_put() {
    print "resource put";
    if ($this->id == -1) {
      $this->exit_error(404, "idRequis");
    }

    // Les parametres passes en put
    parse_str(file_get_contents("php://input"), $_PUT);
    try {
      $db = DemoDB::getConnection();
      //$sql = "SELECT FROM team WHERE team_id=:id";
      //$stmt = $db->prepare($sql);
      //$stmt->bindValue(":id", $this->id);
      //$ok = $stmt->execute();
      //if ($ok) {
      //  $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
      //  $row = $row[0];
      //  if ($row["owner_id"] != $_SESSION['login_id']) {
      //    $this->exit_error(401, "mustBeOwner");
      //  }
      //} else {
      //  $this->exit_error(404);
      //}

      $sql = "UPDATE team SET summary=:summary WHERE team_id=:id";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(":summary", trim($_PUT["summary"]));
      $stmt->bindValue(":id", $this->id);
      $ok = $stmt->execute();
      if ($ok) {
        $this->statusCode = 204;
        $this->body = "";
        // Number of affected rows
        $nb = $stmt->rowCount();
        if ($nb == 0) {
          // No team or not really changed.
          // Check it;
          $sql = "SELECT team_id FROM team WHERE team_id=:id";
          $stmt = $db->prepare($sql);
          $stmt->bindValue(":id", $_GET["id"]);
          $ok = $stmt->execute();
          if ($stmt->fetch() == null) {
            $this->exit_error(404);
          }
        }
      }
      else {
        $erreur = $stmt->errorInfo();
        // si doublon
        if ($erreur[1] == 1062) {
          $this->exit_error(409, "duplicateName");
        }
        else {
          $this->exit_error(409, $erreur[1]." : ".$erreur[2]);
        }
      }
    }
    catch (PDOException $e) {
      $this->exit_error(500, $e->getMessage());
    }
  }
}

// Simply run the resource
TeamResource::run();

?>
