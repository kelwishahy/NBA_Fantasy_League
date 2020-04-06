<?php
// returns the trades for a team

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');

if(empty($_POST['username'])) {
  exit;
}
				
	$query_getAllTrades = "SELECT t.TradeID, t.Status, t.TradeDate FROM Trade t
						";
						
	$query_getAllTrades = "SELECT t.TradeID, t.Status, t.TradeDate, t.Player1Number, t.Player2Number, p1.PlayerName as P1NAME, p2.PlayerName as P2NAME, o1.LeagueParticipantID as O1NAME, o2.LeagueParticipantID as O2Name, p1.NBATeam as P1TEAM, p2.NBATeam as P2TEAM, o1.TeamID AS T1ID, o2.TeamID as T2ID
						FROM Trade t, NBAPlayer p1, NBAPlayer p2, TeamOwnedBy o1, TeamOwnedBy o2
						WHERE p1.NBATeam = t.Player1Team and
							  p1.PlayerNumber = t.Player1Number and
							  p2.NBATeam = t.Player2Team and
							  p2.PlayerNumber = t.Player2Number and
							  o1.TeamID = t.TeamID1 and
							  o2.TeamID = t.TeamID2 and
							  (o1.LeagueParticipantID = '".$_POST['username']."' OR o2.LeagueParticipantID = '".$_POST['username']."')
						ORDER BY t.TradeDate DESC
						";

						
	// SELECT t.TradeID, t.Status, t.TradeDate, t.Player1Number, t.Player2Number, p1.PlayerName as P1NAME, p2.PlayerName as P2NAME, o1.LeagueParticipantID as O1NAME, o2.LeagueParticipantID as O2Name, p1.NBATeam as P1TEAM, p2.NBATeam as P2TEAM FROM Trade t, NBAPlayer p1, NBAPlayer p2, TeamOwnedBy o1, TeamOwnedBy o2 WHERE p1.NBATeam = t.Player1Team and p1.PlayerNumber = t.Player1Number and p2.NBATeam = t.Player2Team and p2.PlayerNumber = t.Player2Number and o1.TeamID = t.TeamID1 and o2.TeamID = t.TeamID2 and (o1.LeagueParticipantID = 'Zach' OR o2.LeagueParticipantID = 'Zach') ORDER BY t.TradeDate DESC
						
    $statement_getAllTrades = oci_parse($db_conn, $query_getAllTrades);
    $r = oci_execute($statement_getAllTrades);
    $nrows = oci_fetch_all($statement_getAllTrades, $res);


// return info
echo json_encode($res);

?>
