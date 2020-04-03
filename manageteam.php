<?php
    //Identify which team is being managed
    if (empty($_COOKIE['teamid'])) {
        header("location: myprofile.php");
        exit;
    }

    $teamid = $_COOKIE['teamid'];

    //Perform some SQL queries to get all the relevant team info

    //Establish database connection
    $db_conn;
    $db_conn = oci_connect("ora_vicp24", "a43444447", "dbhost.students.cs.ubc.ca:1522/stu");
    console_log("dbconn = $db_conn");

    $leagueid="";
    $teamname="";
    $totalpoints="";
    $abbrevname="";

    $query_getTeamInfo = "SELECT teamname, totalpoints, league
                            FROM team
                            WHERE teamid = '".$teamid."'"



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
    </style>

    <!-- <div class="transparent-overlay">
        <h1 style="color:white;
            font-weight:bold;
            text-align:center;
            font-size:400%;"> <?//php echo $username; ?>'s Profile </h1> -->
    </div>
</html>