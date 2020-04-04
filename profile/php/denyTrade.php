<?php
// returns the trades for a team

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');

if(empty($_POST['tradeid'])) {
  exit;
}
				
	$query_updateTrade = "UPDATE Trade
							SET Status='Denied'
							WHERE TradeID = '".$_POST['tradeid']."'
						";

    $statement_updateTrade = oci_parse($db_conn, $query_updateTrade);
    $r = oci_execute($statement_updateTrade);


// return info
echo json_encode($r);

?>
