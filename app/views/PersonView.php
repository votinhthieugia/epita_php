<?php
include("LoginCheck.php");
require_once("../lib/RestApiCall.php");

$person_url = "http://localhost/epita_php/app/resources/PersonResource.php?id=".$_GET["id"];
$person = RestApiCall::do_get($person_url);

include("./template/header.html");
if (count($person) > 0) {
  $person = $person[0];
  include("./template/person.html");
} else {
  print "No person found!";
}
include("./template/footer.html");
?>
