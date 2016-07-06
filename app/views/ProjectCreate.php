<?php
require_once("../lib/RestApiCall.php");
include("LoginCheck.php");

//require_once(__DIR__ . '/../php_console/src/PhpConsole/__autoload.php');
//$handler = PhpConsole\Handler::getInstance();
//$handler->start();

switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":    
      include("./template/projectCreate.html");
    
    break;
  case $_SERVER["REQUEST_METHOD"]:    
    $project_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/ProjectResource.php";
    $ownerId = $_SESSION['login_id'];
    $data = array(
      "ownerId" => $ownerId,
      "title" => $_POST["title"],
      "classId" => $_POST["classId"],
      "subject" => $_POST["subject"],
      "deadline" => $_POST["deadline"]
    );
    $result = RestApiCall::do_post($project_url, $data);
    if ($result == false) {
      $error = true;
    }else
    {
        header("Location: http://".$_SERVER["SERVER_NAME"]."/epita_php/app/views/ProjectView.php");
    }
    // Redirect to team list.
    
    break;
}
?>
