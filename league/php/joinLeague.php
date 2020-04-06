<?php
// This is the PHP script

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');

if(empty($_POST['id']) || empty($_POST['username'])) {
  exit;
}
						
	$query_joinLeague = "INSERT INTO Team
						values('".$_POST['username']."''s team', 0 , (SELECT MAX(TeamID) FROM Team) + 100, '".$_POST['id']."')
						";
						
    $statement_joinLeague = oci_parse($db_conn, $query_joinLeague);
    $r = oci_execute($statement_joinLeague);
	
	if(!$r) exit;
	
	$query_assignTeam = "INSERT INTO TeamOwnedBy
						values((SELECT MAX(TeamID) FROM Team), '".$_POST['username']."')
						";
						
    $statement_assignTeam = oci_parse($db_conn, $query_assignTeam);
    $r1 = oci_execute($statement_assignTeam);


// return info
echo json_encode($r and $r1);

?>
