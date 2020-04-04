<?php
// returns the trades for a team

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');

if(empty($_POST['teamid'])) {
  exit;
}
				
	$query_getTeamTrades = "SELECT t.TradeID, t.Status, t.TradeDate, t.Player1Number, t.Player2Number, p1.PlayerName as P1NAME, p2.PlayerName as P2NAME, t1.TeamName as T1NAME, t2.TeamName as T2NAME
						FROM Trade t, NBAPlayer p1, NBAPlayer p2, Team t1, Team t2
						WHERE p1.NBATeam = t.Player1Team and
							  p1.PlayerNumber = t.Player1Number and
							  p2.NBATeam = t.Player2Team and
							  p2.PlayerNumber = t.Player2Number and
							  t1.TeamID = t.TeamID1 and
							  t2.TeamID = t.TeamID2 and
							  (t.TeamID1 = '".$_POST['teamid']."' OR t.TeamID2 = '".$_POST['teamid']."')
						ORDER BY t.TradeDate DESC
						";

    $statement_getTeamTrades = oci_parse($db_conn, $query_getTeamTrades);
    $r = oci_execute($statement_getTeamTrades);
    $nrows = oci_fetch_all($statement_getTeamTrades, $res);


// return info
echo json_encode($res);

?>
