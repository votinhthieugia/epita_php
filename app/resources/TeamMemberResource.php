<?php
require_once("DemoDB.php");
require_once("HttpResource.php");

class TeamMemberResource extends HttpResource {
  protected $team_id;

  public function init() {
    if (isset($_GET["team_id"])) {
      if (is_numeric($_GET["team_id"])) {
        $this->team_id = 0 + $_GET["team_id"]; // transformer en numerique
        if (!is_int($this->team_id) || $this->team_id<= 0) {
          $this->exit_error(400, "idNotPositiveInteger");
        }
      }
      else {
        $this->exit_error(400, "idNotPositiveInteger");
      }
    }
    else {
      $this->exit_error(400, "idNotPositiveInteger");
    }
  }

  protected function is_owner($owner_id) {
    try {
      $db = DemoDB::getConnection();
      $sql = "SELECT owner_id FROM team WHERE team_id=:team_id and owner_id=:owner_id";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(":owner_id", $owner_id);
      $stmt->bindValue(":team_id", $this->team_id);
      $ok = $stmt->execute();
      if ($ok) {
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($row != null && count($row) > 0) {
          return true;
        }
        $this->exit_error(401, "mustBeOwner");
      } else {
        $this->exit_error(500, print_r($db->errorInfo(), true));
      }
    } catch (PDOException $e) {
      $this->exit_error(500, $e->getMessage());
    }
  }

  protected function do_get() {
    // Call the parent
    parent::do_get();

    try {
      $db = DemoDB::getConnection();
      $excluded = $_GET['excluded'];
      if (isset($excluded) && $excluded != '') {
        $sql = "SELECT p.person_id, p.first_name, p.last_name from person as p join class_member as cm on p.person_id=cm.person_id WHERE cm.class_id=
        (
          SELECT class_id from project where project_id = 
          (
            SELECT project_id from team where team_id=:team_id
          )
        ) and p.person_id not in (
          SELECT p.person_id from person as p join team_member as m on p.person_id=m.student_id WHERE m.team_id=:t_id
        )";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":team_id", $_GET["team_id"]);
        $stmt->bindValue(":t_id", $_GET["team_id"]);
        $ok = $stmt->execute();
        if ($ok) {
          $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if ($row != null) {
            $this->statusCode = 200;
            $this->headers[] = "Content-type: text/json; charset=utf-8";
            $this->body = json_encode($row);
          } else {
            $this->exit_error(404);
          }
        }
        else {
          $this->exit_error(500, print_r($db->errorInfo(), true));
        }
      } else {
        $sql = "SELECT p.person_id, p.first_name, p.last_name from person as p join team_member as m on p.person_id=m.student_id WHERE m.team_id=:team_id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":team_id", $_GET["team_id"]);
        $ok = $stmt->execute();
        if ($ok) {
          $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if ($row != null) {
            $this->statusCode = 200;
            $this->headers[] = "Content-type: text/json; charset=utf-8";
            $this->body = json_encode($row);
          } else {
            $this->exit_error(404);
          }
        }
        else {
          $this->exit_error(500, print_r($db->errorInfo(), true));
        }
      }
    } catch (PDOException $e) {
      $this->exit_error(500, $e->getMessage());
    }
  }

  protected function do_post() {
    if (!$this->is_owner($_POST['owner_id'])) {
      $this->exit_error(401, "mustBeOwner");
    }
    try {
      $db = DemoDB::getConnection();
      $sql = "INSERT INTO team_member(team_id, student_id) VALUES (?, ?)";
      $stmt = $db->prepare($sql);
      $data = array($this->team_id, $_POST["student_id"]);
      $ok = $stmt->execute($data);
      if ($ok) {
        $this->statusCode = 204;
        $this->body = "";
        $nb = $stmt->rowCount();
        if ($nb == 0) {
          $this->exit_error(404);
        }
      }
      else {
        $erreur = $stmt->errorInfo();
        $this->exit_error(409, $erreur[1]." : ".$erreur[2]);
      }
    }
    catch (PDOException $e) {
      $this->exit_error(500, $e->getMessage());
    }
  }

  protected function do_delete() {
    parse_str(file_get_contents("php://input"), $_DELETE);
    print "abc".$_DELETE['owner_id'].'adfds'.$this->team_id." ".$_DELETE['student_id'];
    if (!$this->is_owner($_DELETE['owner_id'])) {
      $this->exit_error(401, "mustBeOwner");
    }

    try {
      $db = DemoDB::getConnection();
      $sql = "DELETE from team_member WHERE team_id=:team_id AND student_id=:student_id";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(":team_id", $this->team_id);
      $stmt->bindValue(":student_id", $_DELETE['student_id']);
      $ok = $stmt->execute();
      if ($ok) {
        $this->statusCode = 204;
        $this->body = "";
        $nb = $stmt->rowCount();
        if ($nb == 0) {
          $this->exit_error(404);
        }
      }
      else {
        $erreur = $stmt->errorInfo();
        $this->exit_error(409, $erreur[1]." : ".$erreur[2]);
      }
    }
    catch (PDOException $e) {
      $this->exit_error(500, $e->getMessage());
    }
  }
}

TeamMemberResource::run();
?>
