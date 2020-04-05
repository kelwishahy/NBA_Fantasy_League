<?php
// accepts a trade

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');

if(empty($_POST['tradeid']) || empty($_POST['p1num']) || empty($_POST['p2num']) || empty($_POST['p1team']) || empty($_POST['p2team']) || empty($_POST['t1id']) || empty($_POST['t2id'])) {
  exit;
}
	
	$query_updateP1 = "UPDATE PlayersInTeam
							SET TeamID='".$_POST['t2id']."'
							WHERE PlayerNumber = '".$_POST['p1num']."' and PlayerTeam = '".$_POST['p1team']."'
						";
						
	$statement_updateP1 = oci_parse($db_conn, $query_updateP1);
    $r1 = oci_execute($statement_updateP1);
						
	$query_updateP2 = "UPDATE PlayersInTeam
							SET TeamID='".$_POST['t1id']."'
							WHERE PlayerNumber = '".$_POST['p2num']."' and PlayerTeam = '".$_POST['p2team']."'
						";
						
	$statement_updateP2 = oci_parse($db_conn, $query_updateP2);
    $r2 = oci_execute($statement_updateP2);
	
	$query_updateTrade = "UPDATE Trade
							SET Status='Accepted'
							WHERE TradeID = '".$_POST['tradeid']."'
						";

    $statement_updateTrade = oci_parse($db_conn, $query_updateTrade);
    $r3 = oci_execute($statement_updateTrade);


// return info
echo json_encode($r1 and $r2 and $r3);

?>
