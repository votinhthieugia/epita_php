<?php
include("LoginCheck.php");
require_once("../lib/RestApiCall.php");

$param = "";
$is_all = true;
if (isset($_GET["id"])) {
  $param = "id=".$_GET["id"];
  $is_all = false;
}
$team_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/TeamResource.php?".$param;
$team = RestApiCall::do_get($team_url);

if (count($team) > 0) {
  // Check owner.
  $members = array();
  $team_member_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/TeamMemberResource.php?";
  for ($i = 0; $i < count($team); $i++) {
    $data = array(
      "owner_id" => $team[$i]["owner_id"],
      "team_id" => $team[$i]["team_id"]
    );
    $members[$i] = RestApiCall::do_get($team_member_url.http_build_query($data));
  }

  include("./template/header.html");
  include("./template/team.html");
  include("./template/footer.html");
} else {
  print "No team found!";
}

?>

