<?php
// returns the position of a team in a league

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');

if(empty($_POST['teamid']) || empty($_POST['leagueid']) || empty($_POST['points'])) {
  exit;
}
				
	$query_getTeamPos = "SELECT 1+COUNT(t.TeamID) as TeamPos
						FROM Team t where t.TeamID != '".$_POST['teamid']."' and t.TotalPoints > '".$_POST['points']."' 
						";

    $statement_getTeamPos = oci_parse($db_conn, $query_getTeamPos);
    $r = oci_execute($statement_getTeamPos);
    $nrows = oci_fetch_all($statement_getTeamPos, $res);


// return info
echo json_encode($res);

?>
