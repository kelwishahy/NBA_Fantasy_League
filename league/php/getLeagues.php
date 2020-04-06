<?php
// This is the PHP script

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');

if(empty($_POST['username'])) {
  exit;
}
						
	$query_getLeagues = "SELECT l.LeagueName, l.LeagueID, o.Logo, COUNT(t.TeamID) as TEAMS
						FROM League l
						LEFT JOIN LeagueLogo o on l.ManagedBy = o.ManagedBy
						LEFT JOIN Team t on t.League = l.LeagueID
						WHERE NOT EXISTS (SELECT t1.TeamID FROM Team t1
											LEFT JOIN TeamOwnedBy p1 on p1.TeamID = t1.TeamID 
											WHERE t1.League = l.LeagueID
											AND p1.LeagueParticipantID = '".$_POST['username']."')
						GROUP BY l.LeagueName, l.LeagueID, o.Logo
						";
						
	/*$query_getLeagues = "SELECT l.LeagueName, l.LeagueID, o.Logo
						FROM League l, LeagueLogo o
						WHERE l.ManagedBy = o.ManagedBy";*/
						
    $statement_getLeagues = oci_parse($db_conn, $query_getLeagues);
    $r = oci_execute($statement_getLeagues);
    $nrows = oci_fetch_all($statement_getLeagues, $res);
	
// return info
echo json_encode($res);

?>
