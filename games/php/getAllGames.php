<?php
// get all games

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');
	
	$query_getGames = "SELECT FinalScore, DatePlayed, CompetingTeams
						FROM NBAGame
						ORDER BY DatePlayed DESC
						";

    $statement_getGames = oci_parse($db_conn, $query_getGames);
    $r = oci_execute($statement_getGames);
    $nrows = oci_fetch_all($statement_getGames, $res);

// return info
echo json_encode($res);

?>
