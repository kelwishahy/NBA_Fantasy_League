<?php
// returns the trades for a team

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');
			
	$query_getTeamTrades = "SELECT MAX(TradeID) FROM Trade";

    $statement_getTeamTrades = oci_parse($db_conn, $query_getTeamTrades);
    $r = oci_execute($statement_getTeamTrades);
    $nrows = oci_fetch_all($statement_getTeamTrades, $res);


// return info
echo json_encode($res);

?>
