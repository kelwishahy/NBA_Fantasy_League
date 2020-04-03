<?php
    //Logger for debugging
    include "./chromelogger/ChromePhp.php";

    //Identify which team is being managed
    if (empty($_COOKIE['teamid'])) {
        header("location: myprofile.php");
        exit;
    }

    $teamid = $_COOKIE['teamid'];
    ChromePhp::log("Teamid is $teamid");

    //Perform some SQL queries to get all the relevant team info

    //Establish database connection
    $db_conn;
    $db_conn = oci_connect("ora_vicp24", "a43444447", "dbhost.students.cs.ubc.ca:1522/stu");
    ChromePhp::log("dbconn = $db_conn");

    $leagueid="";
    $teamname="";
    $totalpoints="";
    $abbrevname="";

    $query_getTeamInfo = "SELECT T.teamname, TA.abbrevname, T.totalpoints, T.league 
                            FROM team T, teamabbreviation TA
                            WHERE teamid = '".$teamid."'
                            AND T.teamname = TA.teamname";
    
    $statement_getTeamInfo = oci_parse($db_conn, $query_getTeamInfo);
    $r = oci_execute($statement_getTeamInfo);
    $nrows = oci_fetch_all($statement_getTeamInfo, $res);
    ChromePhp::log($res);

    $leagueid = (int)$res['LEAGUE'][0];
    ChromePhp::log("League ID is $leagueid");
    $teamname = trim($res['TEAMNAME'][0]);
    ChromePhp::log("Team name is $teamname");
    $totalpoints = (int)$res['TOTALPOINTS'][0];
    ChromePhp::log("Total points: $totalpoints");
    $abbrevname = trim($res['ABBREVNAME'][0]);
    ChromePhp::log("Abbreviated name is $abbrevname");



    //BUTTONS
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
        header("location: myprofile.php");
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
                font-size:400%;"> <?php echo $teamname; ?> (<?php echo $abbrevname; ?>) </h1>

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
    </div>
</html>