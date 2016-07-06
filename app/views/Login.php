<?php
session_start();
require_once("../lib/RestApiCall.php");


$messages = array();

switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":
    include("./template/header.html");
    include("./template/login.html");
    include("./template/footer.html");
    break;
  case "POST":
    $person = do_login();
    break;
  default:
    die("Not implemented");
    break;
}

function do_login() {
  $username = empty($_POST["email"]) ? "" : trim($_POST["email"]);
  $password = empty($_POST["password"]) ? "" : trim($_POST["password"]);
  $login_url = "http://localhost/epita_php/app/resources/LoginResource.php";
  $data = array("email" => $username, "password" => $password);
  $person = RestApiCall::do_post($login_url, http_build_query($data));
  if (count($person) > 0) {
    $person = $person[0];
    $_SESSION["login_id"] = $person["person_id"];
    $_SESSION["name"] = $person["first_name"]." ".$person["last_name"];
    $_SESSION["email"] = $person["email"];
    $_SESSION["password"] = $person["password"];
    if ($person["is_trainer"]) {
      header("Location: /epita_php/app/views/ProjectView.php");
    } else {
      header("Location: /epita_php/app/views/TeamView.php");
    }
  } else {
    include("./template/login.html");
  }
}

?>
