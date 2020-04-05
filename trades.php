<?php
    if (empty($_COOKIE['username'])) {
        header("location: index.php");
        exit;
    }

    $username = $_COOKIE['username'];

    
    console_log("$username");

    if (isset($_POST['logout'])) {
        logout();
    }

    if (isset($_POST['requestTrade'])) {
        requestTrade();
    }

    global $db_conn;
    $db_conn = oci_connect("ora_vicp24", "a43444447", "dbhost.students.cs.ubc.ca:1522/stu");

    //Variable definitions

    //This is hardcoded for now, somehow from the TradeManager page I will be passed the teamID and leagueID
    $teamid = $_COOKIE['teamid'];
    $leagueid = $_COOKIE['leagueid'];



    $query1 = "SELECT NP.playername, NP.nbateam, NP.playernumber, NP.points
    FROM nbaplayer NP
    JOIN playersinteam TP on NP.nbateam=TP.playerteam AND NP.playernumber=TP.playernumber
    WHERE TP.teamid='".$teamid."'
    ORDER BY TP.teamid
    ";

    $statement1 = oci_parse($db_conn, $query1);
    $r = oci_execute($statement1);


    $query2 = "SELECT NP.playername, TP.teamid
    FROM nbaplayer NP
    JOIN playersinteam TP on NP.nbateam=TP.playerteam AND NP.playernumber=TP.playernumber
    WHERE TP.teamid<>'".$teamid."'
    ORDER BY TP.teamid
    ";

    $statement2 = oci_parse($db_conn, $query2);
    $r1 = oci_execute($statement2);

    $query_points = "SELECT NP.points
    FROM nbaplayer NP
    JOIN playersinteam TP on NP.nbateam=TP.playerteam AND NP.playernumber=TP.playernumber
    WHERE TP.teamid<>'".$teamid."'
    ORDER BY TP.teamid
    ";

    $query_number = "SELECT NP.playernumber
    FROM nbaplayer NP
    JOIN playersinteam TP on NP.nbateam=TP.playerteam AND NP.playernumber=TP.playernumber
    WHERE TP.teamid<>'".$teamid."'
    ORDER BY TP.teamid
    ";

    $query_team = "SELECT NP.nbateam
    FROM nbaplayer NP
    JOIN playersinteam TP on NP.nbateam=TP.playerteam AND NP.playernumber=TP.playernumber
    WHERE TP.teamid<>'".$teamid."'
    ORDER BY TP.teamid
    ";
    
    if ($_POST && isset($_POST['points'])) {
        $statement_points = oci_parse($db_conn, $query_points);
        $r_1 = oci_execute($statement_points);
    }

    if ($_POST && isset($_POST['number'])) {
        $statement_number = oci_parse($db_conn, $query_number);
        $r_2 = oci_execute($statement_number);
    }

    if ($_POST && isset($_POST['team'])) {
        $statement_team = oci_parse($db_conn, $query_team);
        $r_3 = oci_execute($statement_team);
    }

    function requestTrade() {
        $p1team = strval($_POST["p1team"]);
        $p1num = (int)($_POST["p1num"]);
        $p2team = strval($_POST["p2team"]);
        $p2num = (int)($_POST["p2num"]);
        $p2teamid = (int)($_POST["p2teamid"]);

        // get the next trade id
        $db_conn = oci_connect("ora_vicp24", "a43444447", "dbhost.students.cs.ubc.ca:1522/stu");
        $query_getMaxTradeID = "SELECT * FROM (
            SELECT * FROM Trade ORDER BY TradeID DESC
            ) WHERE ROWNUM = 1";
        $statement_getMaxTradeID = oci_parse($db_conn, $query_getMaxTradeID);
        $r2 = oci_execute($statement_getMaxTradeID);
        $nrows1 = oci_fetch_all($statement_getMaxTradeID, $res1);
        $maxid = (int)$res1['TRADEID'][0];
        $next_trade_id = $maxid + 1;

        // hardcode for now
        $teamid_copy = $_COOKIE['teamid'];

        // get today's date
        $today = date("Y-m-d");
        console_log("MY TEAM ID: $teamid_copy");

        $status = "Pending";

        $sql="INSERT INTO Trade VALUES('${next_trade_id}', '${teamid_copy}', '${p2teamid}', '${p1num}', '${p2num}', '".$p1team."', '".$p2team."', '".$status."', '".$today."')";
        $statement_newTrade= oci_parse($db_conn, $sql);
        $r3 = oci_execute($statement_newTrade);    
    }


    function logout() {
        //Invalidate the cookie
        setcookie("username", $username, time()-84000, "/");
    
        //Redirect to login page
        header("location: index.php");
        exit;
    }


    //Print to console for debugging purposes
    function console_log($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);

        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }
?>

<!DOCTYPE html>
<html>
    <style>

    body {
        background-image: url('./images/background.jpg');
        background-attachment: fixed;
        background-size: cover;
        font-family:Arial;
    }

    .transparent-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .row {
        display: flex;
    }

    .column {
        flex: 50%;
        text-align: center;
    }

    form {
        display: inline-block;
    }

    </style>

    <div class="transparent-overlay">
        <h1 style="color:white;
            font-weight:bold;
            text-align:center;
            font-size:400%;"> Trades </h1>
        
        <form action="" method="POST">
            <input type="submit" name="logout" value="Logout"
            style="background-color:#fc9803;
            color:white; 
            border:none;
            top: 0;
            right: 0;
            position: absolute;
            margin-top: 10px;
            margin-right: 10px;
            "></input>
        </form>

        <div class="row">
            <div class="column" >
                <h2 style="color:white; text-align:center;">Your Roster</h2>
                <table style="color:white; margin-left:auto; margin-right:auto; width: 500px;">
                    <tr>
                        <th align="center">Player</th>
                        <th align="center">Team</th>
                        <th align="center">No.</th>
                        <th align="center">Points</th>
                    </tr>
                    <?php while($row = oci_fetch_array($statement1)) { ?>
                        <tr>
                            <td align="center"><?php echo trim($row['PLAYERNAME']); ?></td>
                            <td align="center"><?php echo trim($row['NBATEAM']); ?></td>
                            <td align="center"><?php echo (int)$row['PLAYERNUMBER']; ?></td>
                            <td align="center"><?php echo (int)$row['POINTS']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="column" style="margin-top:20px">
                <h2 style="color:white; text-align:center;">Their Roster</h2>
                <div class = "row">
                    <div class="column">
                        <form>
                            <input type="submit" value="Players"
                            style="background-color:#fc9803;
                            color:white; 
                            border:none;
                            "></input>
                        </form>
                        <table style="color:white; margin-left:auto; margin-right:auto;">
                            <tr>
                                <th align="center">Team ID</th>
                                <th align="center">Name</th>
                            </tr>
                            <?php while($row = oci_fetch_array($statement2)) { ?>
                                <tr>
                                    <td align="center"><?php echo trim($row['TEAMID']); ?></td>
                                    <td align="center"><?php echo trim($row['PLAYERNAME']); ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <div class="column">
                    <form action="" method="POST">
                    <input type="submit" name="points" value="Points"
                    style="background-color:#fc9803;
                    color:white; 
                    border:none;
                    "></input>
                </form>
                <form action="" method="POST">
                    <input type="submit" name="number" value="Number"
                    style="background-color:#fc9803;
                    color:white; 
                    border:none;
                    "></input>
                </form>
                <form action="" method="POST">
                    <input type="submit" name="team" value="Team"
                    style="background-color:#fc9803;
                    color:white; 
                    border:none;
                    "></input>
                </form>
                        <table style="color:white; margin-left:auto; margin-right:auto;">
                            <tr>
                                <th><br></th>
                            </tr>
                            <?php while( ($row2 = oci_fetch_array($statement_points)) || ($row3 = oci_fetch_array($statement_number)) || ($row4 = oci_fetch_array($statement_team)) ) { ?>
                                <tr>
                                    <td align="center"><?php echo trim($row4['NBATEAM']); ?></td>
                                    <td align="center"><?php echo trim($row2['POINTS']); ?></td>
                                    <td align="center"><?php echo trim($row3['PLAYERNUMBER']); ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div style = "text-align:center; margin-top:50px">
                <form action="" method="POST">
                    <label for="p1team" style="color:white">Player Team:</label>
                    <input type="text" id="p1team" name="p1team" placeholder = "Your Player's Team">
                    <label for="p1num" style="color:white">Player Number:</label>
                    <input type="number" id="p1num" name="p1num" placeholder = "Your Player's Number"><br><br>
                    <label for="p2teamid" style="color:white">Team ID:</label>
                    <input type="number" id="p2teamid" name="p2teamid" placeholder = "Their Team ID">
                    <label for="p2team" style="color:white">Player Team:</label>
                    <input type="text" id="p2team" name="p2team" placeholder = "Their Player's Team">
                    <label for="p2num" style="color:white" >Player Number:</label>
                    <input type="number" id="p2num" name="p2num" placeholder = "Their Player's Number"><br><br>
                    <input type="submit" name="requestTrade" value="Request Trade"
                    style="background-color:#fc9803;
                        color:white; 
                        border:none;
                        ">
                    </input>
                </form>
            </div>
        </div>
    </div>


    </div>
</html>