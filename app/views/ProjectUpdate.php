<?php
require_once("../lib/RestApiCall.php");
include("LoginCheck.php");

//require_once(__DIR__ . '/../php_console/src/PhpConsole/__autoload.php');
//$handler = PhpConsole\Handler::getInstance();
//$handler->start();

switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":
    include("./template/header.html");
    if (isset($_GET["id"])) {
      $project_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/ProjectResource.php?id=".$_GET["id"];
      $project = RestApiCall::do_get($project_url);
      if (count($project) > 0) {
        $project = $project[0];
        if($_SESSION['login_id'] == $project["owner_id"]){
            $newDate = date("Y-m-d", strtotime($project["deadline"]));
            include("./template/projectUpdate.html");
        }else{
            print "<h3>You are not the owner of this project!</h3>";
            print "<h4>Owner:".$project["owner_id"]."</h4>";
            print "<h4>You:".$_SESSION['login_id']."</h4>";
        }
      } else {
        print "project not found!";
      }
    } else {
      print "No project specified!";
    }
    include("./template/footer.html");
    break;
  case "POST":
    $team_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/ProjectResource.php?id=".$_POST["project_id"];
    $ownerId = $_SESSION['login_id'];
    //$handler->debug($ownerId, '');
    $data = array(
      "ownerId" => $ownerId,
      "title" => $_POST["title"],
      "deadline" => $_POST["deadline"],
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
