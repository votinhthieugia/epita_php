<?php
require_once("../lib/RestApiCall.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  $projectId = $_GET["projectId"];
  $service_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/ProjectResource.php?id=".$projectId;
  $projects = RestApiCall::do_get($service_url);
  if (count($projects) > 0) {
    $project = $projects[0];
    include("./template/teamCreate.html");
  }
} else {
  //$service_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/TeamResource.php?".$param;
  //$team = RestApiCall::do_get($service_url);
}
?>
