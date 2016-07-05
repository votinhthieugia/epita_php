<?php
require_once("../lib/RestApiCall.php");

require_once(__DIR__ . '/../php_console/src/PhpConsole/__autoload.php');
$handler = PhpConsole\Handler::getInstance();
$handler->start();

switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":
    if (isset($_GET["id"])) {
      $project_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/ProjectResource.php?id=".$_GET["id"];
      $project = RestApiCall::do_get($project_url);
      if (count($project) > 0) {
        $project = $project[0];
        include("./template/projectUpdate.html");
      } else {
        print "project not found!";
      }
    } else {
      print "No project specified!";
    }
    break;
  case "POST":
    $team_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/ProjectResource.php?id=".$_POST["project_id"];
    $ownerId = 1;//$_SESSION['login_id'];
    $data = array(
      "ownerId" => $ownerId,
      "title" => $_POST["title"],
      //"deadline" => $_POST["deadline"],
      "subject" => $_POST["subject"]
    );
    $result = RestApiCall::do_put($team_url, $data);
    if ($result == false) {
      $error = true;
    } else {
      header("Location: http://".$_SERVER["SERVER_NAME"]."/epita_php/app/views/ProjectView.php");
    }
    break;
}

?>
