<?php
// get all games

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');
	
if(empty($_POST['num']) || empty($_POST['team'])) {
  exit;
}
						
	$query_getGames = "SELECT g.FinalScore, g.DatePlayed, g.CompetingTeams
						FROM NBAGame g, PlayerInGame ig
						WHERE ig.GameDate = g.DatePlayed AND ig.CompetingTeams = g.CompetingTeams
						AND ig.PlayerNumber = '".$_POST['num']."'
						AND ig.PlayerTeam = '".$_POST['team']."'
						ORDER BY DatePlayed DESC
						";

    $statement_getGames = oci_parse($db_conn, $query_getGames);
    $r = oci_execute($statement_getGames);
    $nrows = oci_fetch_all($statement_getGames, $res);

// return info
echo json_encode($res);

?>
