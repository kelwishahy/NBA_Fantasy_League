<?php
// This is the PHP script

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');

if(empty($_POST['username'])) {
  exit;
}
						
	$query_getTeams = "SELECT t. TeamID, t.TeamName, t.TotalPoints, l.LeagueName, o.Logo, l.LeagueID
                        FROM Team t
						JOIN TeamOwnedBy y on t.TeamID = y.TeamID
						JOIN League l on t.League = l.LeagueID
						LEFT JOIN LeagueLogo o on o.ManagedBy = l.ManagedBy
						WHERE y.LeagueParticipantID = '".$_POST['username']."'";

    $statement_getTeams = oci_parse($db_conn, $query_getTeams);
    $r = oci_execute($statement_getTeams);
    $nrows = oci_fetch_all($statement_getTeams, $res);


// return info
echo json_encode($res);

?>
