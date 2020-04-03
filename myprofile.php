<?php
    //Logger for debugging
    include "./chromelogger/ChromePhp.php";

    if (empty($_COOKIE['username'])) {
        header("location: index.php");
        exit;
    }

    $username = $_COOKIE['username'];
    ChromePhp::log("$username");

    //Connect to the database
    $db_conn;
    $db_conn = oci_connect("ora_vicp24", "a43444447", "dbhost.students.cs.ubc.ca:1522/stu");

    //Get teamid and set it in a cookie
    $teamid="";
    $query_getTeamID = "SELECT teamid
                        FROM teamownedby
                        WHERE leagueparticipantid = '".$username."'
                        ";
    
    $statement_getTeamID = oci_parse($db_conn, $query_getTeamID);
    $r = oci_execute($statement_getTeamID);
    $nrows = oci_fetch_all($statement_getTeamID, $res);
    ChromePhp::log("User owns $nrows team(s)");

    ChromePhp::log($res);
    $teamid = $res["TEAMID"][0];
    ChromePhp::log($teamid);
    ChromePhp::log("Selected teamid $teamid");


    //Logout functionality
    if (isset($_POST['logout'])) {
        logout();
    }

    function logout() {
        //Invalidate the cookie
        setcookie("username", $username, time()-84000, "/");
    
        //Redirect to login page
        header("location: index.php");
        exit;
    }

    //Navigate to team manager
    if (isset($_POST['manage'])) {
        //Create a cookie
        setcookie("teamid", $teamid, time()+1800, "/");

        //Redirect to team manager page
        header("location: manageteam.php");
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
            font-size:400%;"> <?php echo $username; ?>'s Profile </h1>
        
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
            <input type="submit" name="manage" value="Manage Team"
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