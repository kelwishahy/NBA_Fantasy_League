<?php
    //Identify the current team
    if (empty($_COOKIE['teamid'])) {
        header("location: ./profile/profile.html");
        exit;
    }
    $teamid = $_COOKIE['teamid'];
    
    //Establish database connection
    $db_conn;
    $db_conn = oci_connect("ora_vicp24", "a43444447", "dbhost.students.cs.ubc.ca:1522/stu");

    //SQL QUERIES----------------------------------------------------------------------------------------------------

    //1) Get all players
    $query_getAllPlayers = "SELECT PlayerName
                            FROM NBAPlayer
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
        header("location: playersearch.php");
        exit;
    }

    //Get all players
    if (isset($_POST['searchall'])) {
        $statement_getPlayers = oci_parse($db_conn, $query_getAllPlayers);
        oci_execute($statement_getPlayers);
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
                font-size:350%;">View All Players</h1>
    
    <center>
    <!-- Returned Players -->
    <h2 style="color:white; margin-left:20px;">Search Results</h2>
    <table style="color:white; margin-left:20px; width: 600px;">
    <tr>
        <th align="left"><?php if(isset($_POST['searchall']) && isset($_POST['player'])){echo "Player";}?></th>
        <th align="left"><?php if(isset($_POST['searchall']) && isset($_POST['nbateam'])){echo "NBA Team";}?></th>
        <th align="left"><?php if(isset($_POST['searchall']) && isset($_POST['playernumber'])){echo "No.";}?></th>
        <th align="left"><?php if(isset($_POST['searchall']) && isset($_POST['points'])){echo "Points";}?></th>
        <th align="left"><?php if(isset($_POST['searchall']) && isset($_POST['position'])){echo "Position";}?></th>
    </tr>
        <?php while($row = oci_fetch_array($statement_getPlayers)) { ?>
            
            <tr>
            <td><?php if(isset($_POST['searchall']) && isset($_POST['player'])){echo trim($row['PLAYERNAME']);} ?></td>
            <td><?php if(isset($_POST['searchall']) && isset($_POST['nbateam'])){echo trim($row['NBATEAM']);} ?></td>
            <td><?php if(isset($_POST['searchall']) && isset($_POST['playernumber'])){echo $row['PLAYERNUMBER'];} ?></td>
            <td><?php if(isset($_POST['searchall']) && isset($_POST['points'])){echo $row['POINTS'];}?></td>
            <td><?php if(isset($_POST['searchall']) && isset($_POST['position'])){echo $row['POSITION'];} ?></td>
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
    <form action="" method="POST" style="
            color:white; 
            top: 0;
            left: 0;
            position: absolute;
            margin-top: 50px;
            margin-left: 10px;">
        <input type='checkbox' name='player' value='player'>Player<br>
        <input type='checkbox' name='nbateam' value='nbateam'>NBA Team<br>
        <input type='checkbox' name='playernumber' value='playernumber'>Jersey Number<br>
        <input type='checkbox' name='points' value='points'>Points<br>
        <input type='checkbox' name='position' value='position'>Position<br>
        <center><input type="submit" name="searchall" value="Search All Players" style="background-color:#fc9803; color:white; border:none;"></input></center>
    </form>
</html>