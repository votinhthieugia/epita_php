<?php
require_once("../lib/RestApiCall.php");

switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":
    $projectId = $_GET["projectId"];
    $project_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/ProjectResource.php?id=".$projectId;
    $project = RestApiCall::do_get($project_url);
    if (count($project) > 0) {
      $project = $project[0];
      include("./template/teamCreate.html");
    }
    break;
  case $_SERVER["REQUEST_METHOD"]:
    $team_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/TeamResource.php";
    $ownerId = 1;//$_SESSION['login_id'];
    $data = array(
      "projectId" => $_POST["projectId"],
      "ownerId" => $ownerId,
      "summary" => $_POST["summary"]
    );
    $result = RestApiCall::do_post($team_url, $data);
    if ($result == false) {
      $error = true;
    }
    // Redirect to team list.
    include("./template/teamCreate.html");
    break;
}
?>
