<?php
require_once("../lib/RestApiCall.php");

$param = "";
if (isset($_GET["id"])) {
  $param = "id=".$_GET["id"];
}
$service_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/TeamResource.php?".$param;
$team = RestApiCall::do_get($service_url);
//print $service_url.count($team);

if (count($team) > 0) {
  include("./template/team.html");
} else {
  print "No team found!";
}

?>

