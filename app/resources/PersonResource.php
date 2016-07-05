<?php

require_once("HttpResource.php");
require_once("DemoDB.php");

#require_once(__DIR__ . '/../php_console/src/PhpConsole/__autoload.php');
#$handler = PhpConsole\Handler::getInstance();
#$handler->start();

class PersonResource extends HttpResource {
  /** Person id */
  protected $id;

  /** Initialize $id. Send 400 if id missing or not positive integer */
  public function init() {
	  	
    if (isset($_GET["id"])) {
      if (is_numeric($_GET["id"])) {
        $this->id = 0 + $_GET["id"]; // transformer en numerique\
        if (!is_int($this->id) || $this->id <= 0) {
          $this->exit_error(400, "idNotPositiveInteger");
        }
      }
      else {
        $this->exit_error(400, "idNotPositiveInteger");
      }
    }
    else {
      $this->id = -1; // transformer en numerique\
    }
  }

  protected function do_get(){
    // Call the parent
    parent::do_get();
    try {
      $db = DemoDB::getConnection();
      if($this->id == -1){
		$sql = "SELECT * FROM person";  
		$stmt = $db->prepare($sql);
	  }else{
		$sql = "SELECT * FROM person WHERE person_id=:id";  
		$stmt = $db->prepare($sql);
		$stmt->bindValue(":id", $this->id);
	  }
      //$stmt = $db->prepare($sql);
      //$stmt->bindValue(":projectId", $this->id);
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

  /** Is the request sent by an admin?
   * Very basic answer here: only user admin (password admin)
   * is admin. In realistic cases, we should access the DB.
   * @return type
   */
  protected function is_admin() {
   $result = false;
    if (isset($_SERVER["PHP_AUTH_USER"])) {
      $result = $_SERVER["PHP_AUTH_USER"] == "admin"
              && $_SERVER["PHP_AUTH_PW"] == "admin";
    }
    return $result;

  }

  protected function do_put() {
    if (!$this->is_admin()) {
      $this->exit_error(401, "mustBeAdmin");
    }
    // Les parametres passes en put
    parse_str(file_get_contents("php://input"), $_PUT);
    if (empty($_PUT["name"])) {
      $this->exit_error(400, "nameMandatoryAndNotEmpty");
    }
    else {
      try {
        $db = DemoDB::getConnection();
        $sql = "UPDATE person SET name=:name WHERE person_id=:id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":name", ucwords(trim($_PUT["name"])));
        $stmt->bindValue(":id", $this->id);
        $ok = $stmt->execute();
        if ($ok) {
          $this->statusCode = 204;
          $this->body = "";
          // Number of affected rows
          $nb = $stmt->rowCount();
          if ($nb == 0) {
            // No person or not really changed.
            // Check it;
            $sql = "SELECT person_id FROM person WHERE person_id=:id";
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

  protected function do_delete() {
    if (!$this->is_admin()) {
      $this->exit_error(401);
    }
    if (empty($_GET["id"])) {
      $this->exit_error(400, "idRequired");
    }
    try {
      $db = DemoDB::getConnection();
      $sql = "DELETE FROM person WHERE person_id=:id";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(":id", $this->id);
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

// Simply run the resource
PersonResource::run();
?>
