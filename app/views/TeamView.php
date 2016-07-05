<?php
require_once("../lib/RestApiCall.php");

$param = "";
if (isset($_GET["id"])) {
  $param = "id=".$_GET["id"];
}
$team_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/TeamResource.php?".$param;
$team = RestApiCall::do_get($team_url);

if (count($team) > 0) {
  include("./template/team.html");
} else {
  print "No team found!";
}

?>

