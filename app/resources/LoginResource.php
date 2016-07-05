<?php
require_once("DemoDB.php");
require_once("HttpResource.php");

class LoginResource extends HttpResource {

  protected function do_post() {
    // Call the parent
    parent::do_post();
    try {
      $db = DemoDB::getConnection();
      $sql = "SELECT * FROM person WHERE email=:email and password=:password";  
      $stmt = $db->prepare($sql);
      $stmt->bindValue(":email", $_POST['email']);
      $stmt->bindValue(":password", $_POST['password']);
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
};

LoginResource::run();
?>
