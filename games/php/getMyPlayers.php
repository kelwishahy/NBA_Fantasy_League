<?php
// This is the PHP script

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');

if(empty($_POST['teamid'])) {
  exit;
}

	$query_getPlayers = "SELECT p.PlayerNumber, p.NBATeam, p.PlayerName
						FROM NBAPlayer p, PlayersInTeam t
						WHERE t.PlayerNumber = p.PlayerNumber AND
								t.PlayerTeam = p.NBATeam AND
								t.TeamID = '".$_POST['teamid']."'
						";
						
    $statement_getPlayers = oci_parse($db_conn, $query_getPlayers);
    $r = oci_execute($statement_getPlayers);
    $nrows = oci_fetch_all($statement_getPlayers, $res);


// return info
echo json_encode($res);

?>
