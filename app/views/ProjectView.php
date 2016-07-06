<?php

include("LoginCheck.php");

//DEBUG
require_once(__DIR__ . '/../php_console/src/PhpConsole/__autoload.php');
$handler = PhpConsole\Handler::getInstance();
$handler->start();

$messages = array();

include('./template/header.html');
switch ($_SERVER["REQUEST_METHOD"]) {
  case "GET":
    get_project_info();
    break;
  default:
    die("Not implemented");
    break;
}

function get_project_info() {
	
	$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
	$response = json_decode(file_get_contents('http://localhost/epita_php/app/resources/ProjectResource.php',false,$context));
	$columns = ['owner_id', 'class_id', 'title', 'created_at', 'deadline', 'subject'];
	
  print <<<END_FORM
  <form method="POST">
    <div class="container">
  <h2>All projects</h2>
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
		
		$personsNotInTeam = json_decode(file_get_contents('http://localhost/epita_php/app/resources/PersonNotInTeamResource.php?project_id='.$item->project_id.'&class_id='.$item->class_id, false, $context));
		$teams = json_decode(file_get_contents('http://localhost/epita_php/app/resources/TeamResource.php?project_id='.$item->project_id, false, $context));
		
        print "<td>";
        if($_SESSION['login_id'] == $item->owner_id)
        {            
            print "<a href=\"/epita_php/project/edit/id-" . $item->project_id . "\">";
            print $item->project_id;
            print "</a>";            
        }else{
            print $item->project_id;
        }
        print "</td>";
		
		foreach($columns as $col) { 
			print "<td>";
			print $item->$col;
      if ($col == 'class_id') {?>
        <a href="/epita_php/app/views/ProjectCreate.php?class_id=<?= $item->$col ?>">Create Project</a><?php
      }
			print "</td>";
		}
		print "<td>";
		if (isset($teams))
			foreach($teams as $team) { 
				print "<a href=\"/epita_php/team/id-" . $team->team_id . "\">";
				print $team->team_id;
				print "</a>";
				print ", ";
			}
		print "</td>";
		
		print "<td>";
		if (isset($personsNotInTeam))
			foreach($personsNotInTeam as $person) { 
				print "<a href=\"/epita_php/person/id-" . $person->person_id . "\">";
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
  </form>
END_FORM3;
}

include('./template/footer.html');
?>
