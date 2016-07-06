<?php
require_once("../lib/RestApiCall.php");
include("LoginCheck.php");

switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":
    include("./template/header.html");
    $team_url = "http://localhost/epita_php/app/resources/TeamResource.php?id=".$_GET["id"];
    $team = RestApiCall::do_get($team_url);

    if (count($team) > 0) {
      // Check owner here.
      if (true) {
        $team = $team[0];
        $team_member_url = "http://localhost/epita_php/app/resources/TeamMemberResource.php?";
        $data = array(
          "owner_id" => $team["owner_id"],
          "team_id" => $team["team_id"]
        );
        $members = RestApiCall::do_get($team_member_url.http_build_query($data));
        $data["excluded"] = true;
        $available_students = RestApiCall::do_get($team_member_url.http_build_query($data));
        include("./template/teamMember.html");
      } else {
        print "Need to be owner to continue";
      }
    } else {
      print "No team found!";
    }
    include("./template/footer.html");
    break;
  case "POST":
    $is_delete = $_POST["delete"];
    $team_member_url = "http://localhost/epita_php/app/resources/TeamMemberResource.php?team_id=".$_POST["team_id"];
    $data = array(
      "owner_id" => $_SESSION['login_id'],
      "student_id" => $_POST["student_id"]
    );
    if (isset($is_delete)) {
      $delete_result = RestApiCall::do_delete($team_member_url, http_build_query($data));
    } else {
      $add_result = RestApiCall::do_post($team_member_url, http_build_query($data));
    }
    break;
}
?>
