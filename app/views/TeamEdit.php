<?php
require_once("../lib/RestApiCall.php");
include("LoginCheck.php");

switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":
    include("./template/header.html");
    if (isset($_GET["id"])) {
      $team_url = "http://localhost/epita_php/app/resources/TeamResource.php?id=".$_GET["id"];
      $team = RestApiCall::do_get($team_url);
      if (count($team) > 0) {
        $team = $team[0];
        $project_url = "http://localhost/epita_php/app/resources/ProjectResource.php?id=".$team["project_id"]; 
        $project = RestApiCall::do_get($project_url);
        if (count($project) > 0) {
          $project = $project[0];
        }
        include("./template/teamEdit.html");
      } else {
        print "Team not found!";
      }
    } else {
      print "No team specified!";
    }
    include("./template/footer.html");
    break;
  case "POST":
    $team_url = "http://localhost/epita_php/app/resources/TeamResource.php?id=".$_POST["teamId"];
    $ownerId = $_SESSION['login_id'];
    $data = array(
      "ownerId" => $ownerId,
      "summary" => $_POST["summary"]
    );
    $result = RestApiCall::do_put($team_url, $data);
    if ($result == false) {
      $error = true;
    } else {
      header("Location: http://localhost/epita_php/app/views/TeamView.php?id=".$_POST["teamId"]);
    }
    break;
}

?>
