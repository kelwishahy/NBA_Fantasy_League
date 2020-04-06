<?php
// This is the PHP script

// connect to DB (made a seperate file for convinience)
require_once('../../db_connect.php');

if(empty($_POST['id'])) {
  exit;
}
						
	$query_delTeam = "DELETE FROM Team WHERE TeamID = '".$_POST['id']."'
						";
						
    $statement_delTeam = oci_parse($db_conn, $query_delTeam);
    $r = oci_execute($statement_delTeam);
	
	if(!$r) exit;

// return info
echo json_encode($r);

?>
