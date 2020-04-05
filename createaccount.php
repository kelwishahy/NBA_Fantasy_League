<?php
    //Logger for debugging
    include "./chromelogger/ChromePhp.php";

    //Establish database connection
    $db_conn;
    $db_conn = oci_connect("ora_vicp24", "a43444447", "dbhost.students.cs.ubc.ca:1522/stu");
    ChromePhp::log("dbconn = $db_conn");

    //BUTTON LOGIC---------------------------------------------------------------------------
    //Back to login page 
    if ($_POST && isset($_POST['login'])) {
        //Redirect to login page
        header("location: index.php");
        exit;
    }

    if ($_POST && isset($_POST['createaccount'])) {
        //Check that username and password match
        $username = $_POST['username'];
        $usernameC = $_POST['username_confirm'];
        if ($username === "" or ($username !== $usernameC)) {
            echo "<script> alert('Usernames do not match.'); window.location.href='createaccount.php'; </script>";
        }

        $password = $_POST['password'];
        $passwordC = $_POST['password_confirm'];
        if ($password === "" or ($password !== $passwordC)) {
            echo "<script> alert('Passwords do not match.'); window.location.href='createaccount.php'; </script>";
        }

        //Check that the given username does not already exist in the database
        $query_checkUsername = "SELECT *
                                FROM regularparticipant
                                WHERE username = '".$username."'";
        
        $statement_checkUsername = oci_parse($db_conn, $query_checkUsername);
        $r = oci_execute($statement_checkUsername);
        $nrows = oci_fetch_all($statement_checkUsername, $res);

        if ($nrows != 0) {
            echo "<script> alert('Username already taken'); window.location.href='createaccount.php'; </script>";  
        }

        //Add the new user to the database
        $currentDate = strval(date("Y-m-d"));
        ChromePhp::log("$currentDate");
        $query_insertNewUser = "INSERT INTO RegularParticipant VALUES('".$username."','".$password."','".$currentDate."')";
        ChromePhp::log("$query_insertNewUser");
        $statement_insertNewUser = oci_parse($db_conn, $query_insertNewUser);
        ChromePhp::log("$statement_insertNewUser");

       $result = oci_execute($statement_insertNewUser);
       ChromePhp::log("$result");
       ChromePhp::log(oci_error($statement_insertNewUser));

        $query_checkUsername2 = "SELECT *
                                FROM regularparticipant
                                WHERE username = '".$username."'";
        
        $statement_checkUsername2 = oci_parse($db_conn, $query_checkUsername2);
        $r2 = oci_execute($statement_checkUsername2);
        $nrows2 = oci_fetch_all($statement_checkUsername2, $res2);

        if ($nrows2 > 0 ) {
            echo "<script> alert('Account creation successful'); window.location.href='index.php'; </script>";  
        } else {
            echo "<script> alert('Account creation not successful'); window.location.href='createaccount.php'; </script>";
        }
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
                font-size:350%;">Create Account</h1>
        <center>
         <form action="" method="POST" style="color:white; width:100%;">

            <div style="margin-top:20px;"><label for="username">Username</label></div>
            <div style="margin-top:10px;"><input type="text" name="username" placeholder="Enter a username..."></div>
            <div style="margin-top:10px;"><input type="text" name="username_confirm" placeholder="Confirm your username"></div>

            <div style="margin-top:20px;"><label for="password">Password</label></div>
            <div style="margin-top:10px;"><input type="password" name="password" placeholder="Enter a password..."></div>
            <div style="margin-top:10px;"><input type="password" name="password_confirm" placeholder="Confirm your password"></div>

            <div style="margin-top:10px;"><input type="submit" name="createaccount" value="Submit" style="background-color:#fc9803;
            color:white; 
            border:none;
            "></div>
        </form>
        </center>
        <form action="" method="POST">
            <input type="submit" name="login" value="Back to login page"
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
    </div>
</html>