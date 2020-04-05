<?php
    //Logger for debugging
   // include "./chromelogger/ChromePhp.php";

    //Identify which team is being managed
    if (empty($_COOKIE['teamid'])) {
        header("location: ./profile/profile.html");
        exit;
    }
    $teamid = $_COOKIE['teamid'];

    //ChromePhp::log("Teamid is $teamid");

    //Establish database connection
    $db_conn;
    $db_conn = oci_connect("ora_vicp24", "a43444447", "dbhost.students.cs.ubc.ca:1522/stu");
    //ChromePhp::log("dbconn = $db_conn");

    //SQL QUERIES-----------------------------------------------------------------------------------------------

    //1)
    //Get the league id, team name, total points, and the abbreviated name for this team
    $leagueid="";
    $teamname="";
    $totalpoints="";
    $abbrevname="";

    $query_getTeamInfo = "SELECT T.teamname, TA.abbrevname, T.totalpoints, T.league 
                            FROM team T, teamabbreviation TA
                            WHERE teamid = '".$teamid."'
                            AND T.teamname = TA.teamname";
    
    $statement_getTeamInfo = oci_parse($db_conn, $query_getTeamInfo);
    $r1 = oci_execute($statement_getTeamInfo);
    $nrows1 = oci_fetch_all($statement_getTeamInfo, $res1);
    //ChromePhp::log($res1);

    $leagueid = (int)$res1['LEAGUE'][0];
   // ChromePhp::log("League ID is $leagueid");
    $teamname = trim($res1['TEAMNAME'][0]);
   // ChromePhp::log("Team name is $teamname");
    $totalpoints = (int)$res1['TOTALPOINTS'][0];
   // ChromePhp::log("Total points: $totalpoints");
    $abbrevname = trim($res1['ABBREVNAME'][0]);
 //   ChromePhp::log("Abbreviated name is $abbrevname");

    //2)
    //Retrieve the names, numbers, and NBA team of all players on this fantasy team
    $query_getTeamRoster = "SELECT NP.playername, NP.nbateam, NP.playernumber
                            FROM nbaplayer NP
                            JOIN playersinteam TP on NP.nbateam=TP.playerteam AND NP.playernumber=TP.playernumber
                            WHERE TP.teamid='".$teamid."'
                            ";

    //BUTTON LOGIC---------------------------------------------------------------------------------------------------

    //Logout 
    if (isset($_POST['logout'])) {
        //Invalidate the cookie
        setcookie("username", $username, time()-84000, "/");

        //Redirect to login page
        header("location: index.php");
        exit;
    }

    //My Profile 
    if (isset($_POST['profile'])) {
        //Invalidate the cookie
        setcookie("teamid", $teamid, time()-84000, "/");

        //Redirect to login page
        header("location: ./profile/profile.html");
        exit;
    }

    //View Roster
    if ($_POST && isset($_POST['roster'])) {
        $statement_getTeamRoster = oci_parse($db_conn, $query_getTeamRoster);
        $r2 = oci_execute($statement_getTeamRoster);
    }

    //Update Team Name
    if ($_POST && isset($_POST['updateteamname'])) {
        if (!empty($_POST['teamname']) && !empty($_POST['abbrev'])) {

            $newTeamName = $_POST['teamname'];
            $newTeamAbbrev = $_POST['abbrev'];

            $query_update1 = "UPDATE Team
                                SET TeamName = '".$newTeamName."'
                                WHERE TeamID = '".$teamid."'
            ";

            $query_update2 = "UPDATE TeamAbbreviation
                                SET TeamName = '".$newTeamName."', AbbrevName = '".$newTeamAbbrev."'
                                WHERE TeamName = '".$teamname."'
            ";

            $statement1 = oci_parse($db_conn, $query_update1);
            $result1 = oci_execute($statement1);
            $statement2 = oci_parse($db_conn, $query_update2);
            $result2 = oci_execute($statement2);

            header("location: manageteam.php");
            exit;
        }
    }

    //Search Players
    if ($_POST && isset($_POST['search'])) {
        setcookie("teamid", $teamid, time()+5, "/");
        header("location: playersearch.php");
        exit;
    }

    //Trades
    if ($_POST && isset($_POST['trades'])) {
        setcookie("teamid", $teamid, time()+600, "/");
        setcookie("leagueid", $leagueid, time()+600, "/");
        header("location: playersearch.php");
        exit;
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
    </style>

    <div class="transparent-overlay">
        <h1 style="color:white;
                font-weight:bold;
                text-align:center;
                font-size:350%;"> <?php echo $teamname; ?> (<?php echo $abbrevname; ?>) </h1>
        <h1 style="color:white;
                font-weight:bold;
                text-align:center;
                font-size:200%;"> League #<?php echo $leagueid; ?> - <?php echo $totalpoints; ?> Points</h1>

        <form action="" method="post" accept-charset="utf-8" style="
            padding-top: 20px;
            margin: auto;
            text-align:center;">

            <input type="text" name="teamname" placeholder="Update team name..."></input>
            <input type="text" name="abbrev" placeholder="Update team abbreviation..."></input>
            <input type="submit" name="updateteamname" value="Update" style="background-color:#fc9803; color:white; border:none;"></input>
        </form>
    
    <!-- Roster table -->
    <h2 style="color:white; margin-left:20px;">Roster</h2>
    <table style="color:white; margin-left:20px; width: 300px;">
    <tr>
        <th align="left">Player</th>
        <th align="left">Team</th>
        <th align="left">No.</th>
    </tr>
        <?php while($row = oci_fetch_array($statement_getTeamRoster)) { ?>
            
            <tr>
            <td><?php echo trim($row['PLAYERNAME']); ?></td>
            <td><?php echo trim($row['NBATEAM']); ?></td>
            <td><?php echo (int)$row['PLAYERNUMBER']; ?></td>
            </tr>
        <?php } ?>
    </table>

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
    <form action="" method="POST">
            <input type="submit" name="profile" value="My Profile"
            style="background-color:#fc9803;
            color:white; 
            border:none;
            top: 0;
            right: 0;
            position: absolute;
            margin-top: 30px;
            margin-right: 10px;
            "></input>
        </form>
    <form action="" method="POST">
            <input type="submit" name="roster" value="View Roster"
            style="background-color:#fc9803;
            color:white; 
            border:none;
            top: 0;
            left: 0;
            position: absolute;
            margin-top: 10px;
            margin-left: 10px;
            "></input>
    </form>
    <form action="" method="POST">
            <input type="submit" name="search" value="Search Players"
            style="background-color:#fc9803;
            color:white; 
            border:none;
            top: 0;
            left: 0;
            position: absolute;
            margin-top: 30px;
            margin-left: 10px;
            "></input>
    </form>
    <form action="" method="POST">
            <input type="submit" name="trades" value="Trades"
            style="background-color:#fc9803;
            color:white; 
            border:none;
            top: 0;
            left: 0;
            position: absolute;
            margin-top: 30px;
            margin-left: 10px;
            "></input>
    </form>
    </div>
</html>