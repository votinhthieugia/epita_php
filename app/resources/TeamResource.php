<?php
require_once("DemoDB.php");
require_once("HttpResource.php");

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
      else {
        $this->exit_error(400, "idNotPositiveInteger");
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
      $sql = "INSERT INTO team(owner_id, project_id, summary, created_at) 
              VALUES(:ownerId, :projectId, :summary, :createdAt)";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(":ownerId", $_SESSION["login_id"]);
      $stmt->bindValue(":projectId", $_POST["project_id"]);
      $stmt->bindValue(":summary", $_POST["summary"]);
      $stmt->bindValue(":createdAt", time());
      $ok = $stmt->execute();
      if ($ok) {
      } else {
        $this->exit_error(500);
      }
    } catch (PDOException $e) {
      $this->exit_error(500, $e->getMessage());
    }
  }

  protected function do_put() {
    if ($this->id == -1) {
      $this->exit_error(404, "idRequis");
    }

    // Les parametres passes en put
    parse_str(file_get_contents("php://input"), $_PUT);
    try {
      $db = DemoDB::getConnection();
      $sql = "SELECT FROM team WHERE team_id=:id";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(":id", $this->id);
      $ok = $stmt->execute();
      if ($ok) {
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = $row[0];
        if ($row["owner_id"] != $_SESSION['login_id']) {
          $this->exit_error(401, "mustBeOwner");
        }
      } else {
        $this->exit_error(404);
      }

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
