<?php
    //Logger for debugging
    include "./chromelogger/ChromePhp.php";

    //Identify the current team
    if (empty($_COOKIE['teamid'])) {
        header("location: ./profile/profile.html");
        exit;
    }
    $teamid = $_COOKIE['teamid'];
    ChromePhp::log("Team id is $teamid");

    //Identify the current league
    if (empty($_COOKIE['leagueid'])) {
        header("location: ./profile/profile.html");
        exit;
    }
    $leagueid = $_COOKIE['leagueid'];
    ChromePhp::log("League id is $leagueid");

    //Establish database connection
    $db_conn;
    $db_conn = oci_connect("ora_vicp24", "a43444447", "dbhost.students.cs.ubc.ca:1522/stu");
    ChromePhp::log("dbconn = $db_conn");

    //SQL QUERIES----------------------------------------------------------------------------------------------------

    //1) Retrieve all unavailable players in this league
    $query_getUnavailablePlayers = "SELECT P.playername, P.playernumber, P.points, P.nbateam, T.teamname
                                    FROM nbaplayer P, team T, playersinteam PT
                                    WHERE P.nbateam = PT.playerteam
                                    AND P.playernumber = PT.playernumber
                                    AND T.teamid = PT.teamid
                                    AND T.league = '".$leagueid."'
    ";

    //2) Retrieve all free agents in this league
    $query_getFreeAgents = "SELECT P2.playername, P2.playernumber, P2.points, P2.nbateam
                            FROM nbaplayer P2

                            MINUS

                            SELECT P3.playername, P3.playernumber, P3.points, P3.nbateam
                            FROM nbaplayer P3, Team T3, playersinteam PT3
                            WHERE T3.teamid IN(
                                SELECT T4.teamid
                                FROM team T4
                                WHERE T4.league = '".$leagueid."') 
                            AND PT3.teamid = T3.teamid
                            AND P3.playernumber = PT3.playernumber
                            AND P3.nbateam = PT3.playerteam
    ";

    //3) Retrieve all unavailable players in all leagues
    $query_getUnavailablePlayers_allLeagues = "SELECT DISTINCT P1.PlayerName, P1.PlayerNumber, P1.NBATeam
    FROM League, Team, PlayersInTeam PT, NBAPlayer P1
    WHERE NOT EXISTS (SELECT * 
              FROM League
              WHERE LeagueID 
              NOT IN (SELECT League
                  FROM Team))
    AND Team.League = League.LeagueID
    AND PT.TeamID = Team.TeamID

    MINUS

    SELECT DISTINCT P.PlayerName, P.PlayerNumber, P.NBATeam
    FROM NBAPlayer P, League L1, League L2
    WHERE EXISTS(
        SELECT *
        FROM PlayersInTeam PT, Team T
        WHERE P.PlayerNumber = PT.PlayerNumber
        AND P.NBATeam = PT.PlayerTeam
        AND T.TeamID = PT.TeamID
        AND T.League = L1.LeagueID)
    AND NOT EXISTS(
        SELECT *
        FROM PlayersInTeam PT2, Team T2
        WHERE P.PlayerNumber = PT2.PlayerNumber
        AND P.NBATeam = PT2.PlayerTeam
        AND T2.TeamID = PT2.TeamID
        AND T2.League = L2.LeagueID)
    OR EXISTS(
        SELECT P3.PlayerName, P3.PlayerNumber, P3.NBATeam
        FROM NBAPlayer P3
        WHERE P3.NBATeam = P.NBATeam
        AND P3.PlayerNumber = P.PlayerNumber MINUS SELECT P4.PlayerName, PT4.PlayerNumber, PT4.PlayerTeam
        FROM PlayersInTeam PT4, NBAPlayer P4)
    ";

    //4) Retrieve players who are free agents in all leagues
    $query_getFreeAgents_allLeagues = "SELECT P.PlayerName, P.PlayerNumber, P.NBATeam
                                        FROM NBAPlayer P

                                        MINUS

                                        SELECT P2.PlayerName, PT.PlayerNumber, PT.PlayerTeam
                                        FROM PlayersInTeam PT, NBAPlayer P2
    ";

    //5) Find the average number of points scored by all players on each NBA team
    $query_getNestedAvg = "SELECT NBATeam, AVG(Points)
                            FROM NBAPlayer
                            GROUP BY NBATeam";
    
    //6) Find the highest scoring player(s)
    $query_getHighestScorer = "SELECT PlayerName, PlayerNumber, NBATeam, Points
                                FROM NBAPlayer
                                WHERE Points IN(
                                    SELECT MAX(Points)
                                    FROM NBAPlayer)
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

    //Back
    if (isset($_POST['back'])) {
        //Invalidate the cookie
        setcookie("teamid", $teamid, time()+5, "/");

        //Redirect to manage team
        header("location: manageteam.php");
        exit;
    }

    //Search unavailable players in this league
    if (isset($_POST['search']) && $_POST['status'] === 'unavailable' && $_POST['leagueSetting'] === 'this') {
        $status = $_POST['status'];
        ChromePhp::log("$status");
        $statement_getPlayers = oci_parse($db_conn, $query_getUnavailablePlayers);
        ChromePhp::log("$statement_getPlayers");
        ChromePhp::log(oci_execute($statement_getPlayers));
    }

    //Search Free Agents in this league
    if (isset($_POST['search']) && $_POST['status'] === 'freeagent' && $_POST['leagueSetting'] === 'this') {
        $status = $_POST['status'];
        ChromePhp::log("$status");
        $statement_getPlayers = oci_parse($db_conn, $query_getFreeAgents);
        ChromePhp::log("$statement_getPlayers");
        ChromePhp::log(oci_execute($statement_getPlayers));
        ChromePhp::log(oci_error($statement_getPlayers));
    }

    //Search unavailable players in all leagues
    if (isset($_POST['search']) && $_POST['status'] === 'unavailable' && $_POST['leagueSetting'] === 'all') {
        $status = $_POST['status'];
        ChromePhp::log("$status");
        $statement_getPlayers = oci_parse($db_conn, $query_getUnavailablePlayers_allLeagues);
        ChromePhp::log("$statement_getPlayers");
        ChromePhp::log(oci_execute($statement_getPlayers));
        ChromePhp::log(oci_error($statement_getPlayers));
    }

    //Search free agents in all leagues
    if (isset($_POST['search']) && $_POST['status'] === 'freeagent' && $_POST['leagueSetting'] === 'all') {
        $status = $_POST['status'];
        ChromePhp::log("$status");
        $statement_getPlayers = oci_parse($db_conn, $query_getFreeAgents_allLeagues);
        ChromePhp::log("$statement_getPlayers");
        ChromePhp::log(oci_execute($statement_getPlayers));
        ChromePhp::log(oci_error($statement_getPlayers));
    }

    //Get average points scored by players on all NBA teams
    if (isset($_POST['nestavg'])) {
        $statement_getPlayers = oci_parse($db_conn, $query_getNestedAvg);
        oci_execute($statement_getPlayers);
    }

    //Find the highest scoring player
    if (isset($_POST['highscore'])) {
        $statement_getPlayers = oci_parse($db_conn, $query_getHighestScorer);
        oci_execute($statement_getPlayers);
    }

    //View all players
    if (isset($_POST['viewallplayers'])) {
        //Set cookie
        setcookie("teamid", $teamid, time()+5, "/");

        //Redirect to manage team
        header("location: viewallplayers.php");
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
                font-size:350%;">Player Search</h1>
    
    <center>
    <form action="" method="POST" style="color:white; width:100%;">
        <select name="status">
            <option value="freeagent">Free Agent</option>
            <option value="unavailable">Unavailable</option>
        </select>
        <label for="in">in</label>
        <select name="leagueSetting">
            <option value="this">This League</option>
            <option value="all">All Leagues</option>
        </select>
        <input type="submit" name="search" value="Search" style="background-color:#fc9803; color:white; border:none;"></input>
    </form>
    <form action="" method="POST" style="color:white; width:100%; margin-top:20px;">
        <label for="findavg">Find average points scored by players on all NBA teams:</label>
        <input type="submit" name="nestavg" value="Search" style="background-color:#fc9803; color:white; border:none;"></input>
    </form>
    <form action="" method="POST" style="color:white; width:100%; margin-top:20px;">
        <label for="findHighestScorer">Find the highest scoring player:</label>
        <input type="submit" name="highscore" value="Search" style="background-color:#fc9803; color:white; border:none;"></input>
    </form>

    <form action="" method="POST" style="color:white; width:100%; margin-top:20px;">
        <div><label for="findSpecificPlayer">Find a specific player:</label></div>

        <input type="number" id="number" name="number" placeholder="Jersey Number..."></input>

        <input type="text" id="team" name="team" placeholder="Team..."></input>

        <select name="position">
            <option value="pointguard">Point Guard</option>
            <option value="smallforward">Small Forward</option>
            <option value="center">Center</option>
            <option value="powerforward">Power Forward</option>
            <option value="shootingguard">Shooting Guard</option>
        </select>

        <input type="number" id="points" name="points" placeholder="Points..."></input>

        <input type="text" id="playername" name="playername" placeholder="Name..."></input>
        
        <input type="submit" name="findplayer" value="Search" style="background-color:#fc9803; color:white; border:none;"></input>
    </form>

    <!-- Returned Players -->
    <h2 style="color:white; margin-left:20px;">Search Results</h2>
    <table style="color:white; margin-left:20px; width: 600px;">
    <tr>
        <th align="left">Player</th>
        <th align="left">NBA Team</th>
        <th align="left">No.</th>
        <th align="left"><?php if(isset($_POST['nestavg'])){echo "Avg Points/Player";} else {echo "Points";} ?></th>
        <th align="left">Fantasy Team</th>
    </tr>
        <?php while($row = oci_fetch_array($statement_getPlayers)) { ?>
            
            <tr>
            <td><?php echo trim($row['PLAYERNAME']); ?></td>
            <td><?php echo trim($row['NBATEAM']);?></td>
            <td><?php echo $row['PLAYERNUMBER']; ?></td>
            <td><?php if(array_key_exists("POINTS", $row)){echo $row['POINTS'];} else if(array_key_exists("AVG(POINTS)", $row)){echo round($row['AVG(POINTS)'], 2);}?></td>
            <td><?php echo $row['TEAMNAME']; ?></td>
            </tr>
        <?php } ?>
    </table>
    </center>
    


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
            <input type="submit" name="back" value="Back"
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
            <input type="submit" name="viewallplayers" value="View All Players"
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
</html>