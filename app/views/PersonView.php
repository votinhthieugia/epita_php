<?php
include("LoginCheck.php");
require_once("../lib/RestApiCall.php");

$person_url = "http://".$_SERVER["SERVER_NAME"]."/epita_php/app/resources/PersonResource.php?id=".$_GET["id"];
$person = RestApiCall::do_get($person_url);

if (count($person) > 0) {
  $person = $person[0];
  include("./template/person.html");
} else {
  print "No person found!";
}
?>