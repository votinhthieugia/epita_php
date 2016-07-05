<?php

//DEBUG
#require_once(__DIR__ . '/../php_console/src/PhpConsole/__autoload.php');
#$handler = PhpConsole\Handler::getInstance();
#$handler->start();

$messages = array();

switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":
    get_project_info();
    break;
  case "POST":
    $person = do_login();
    print "Logged in as :".$person[name];
    break;
  default:
    die("Not implemented");
    break;
}

function get_project_info() {
	
	$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
	$response = json_decode(file_get_contents('http://localhost/app/resources/ProjectResource.php',false,$context));
	$columns = ['project_id', 'owner_id', 'class_id', 'title', 'created_at', 'deadline', 'subject'];
	
  print <<<END_FORM
  <form method="POST">
    <div class="container">
  <h2>Results</h2>
  <table class="table">
    <thead>
      <tr>
      	<th>ID</th>
        <th>Owner ID</th>
        <th>Class ID</th>
        <th>Title</th>
        <th>Created at</th>
        <th>Deadline</th>
        <th>Subject</th>
        <th>Teams</th>
        <th>Students not in any team</th>
      </tr>
    </thead>    
    <tbody id="resultsBody">
END_FORM;

	foreach($response as $item) {
		print "<tr>";
		
		$personsNotInTeam = json_decode(file_get_contents('http://localhost/app/resources/PersonNotInTeamResource.php?project_id='.$item->project_id.'&class_id='.$item->class_id, false, $context));
		$teams = json_decode(file_get_contents('http://localhost/app/resources/TeamResource.php?project_id='.$item->project_id, false, $context));
		
		foreach($columns as $col) { 
			print "<td>";
			print $item->$col;
			print "</td>";
		}
		print "<td>";
		if (isset($teams))
			foreach($teams as $team) { 
				print "<a href=\"http://localhost/app/resources/TeamResource.php?id=" . $team->team_id . "\">";
				print $team->team_id;
				print "</a>";
				print ", ";
			}
		print "</td>";
		
		print "<td>";
		if (isset($personsNotInTeam))
			foreach($personsNotInTeam as $person) { 
				print "<a href=\"http://localhost/app/resources/PersonResource.php?id=" . $person->person_id . "\">";
				print $person->first_name . " " . $person->last_name;
				print "</a>";
				print ", ";
			}
		print "</td>";
		
		print "</tr>";
	}

print <<<END_FORM3
	</tbody>
  </table>
  </div>
    <button type="submit">Submit</button>
  </form>
END_FORM3;
}

function do_login() {
  $username = empty($_POST["username"]) ? "" : trim($_POST["username"]);
  $password = empty($_POST["password"]) ? "" : trim($_POST["password"]);
  $person = PersonModel::login($username, $password);
  if (count($person) > 0) {
    return $person;
  } else {
    display_login_form();
  }
}

?>
