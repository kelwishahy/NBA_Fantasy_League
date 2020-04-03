<?php
    //The following code is adapted from https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php

    //Check if the user is already logged in, and redirect to myprofile if they are
    if(isset($_COOKIE["username"])) {
        header("location: myprofile.php");
        exit;
    }

    global $db_conn;
    $db_conn = oci_connect("ora_vicp24", "a43444447", "dbhost.students.cs.ubc.ca:1522/stu");

    //Variable definitions
    $username = "";
    $password = "";

    //The following code takes username & password input and checks the db for its existence
   if($_SERVER["REQUEST_METHOD"] == "POST") {
       console_log("dbconn = $db_conn");
 
        $username = strval($_POST["username"]);
        $password = strval($_POST["password"]);

        console_log("Given username is $username");
        console_log("Given password is $password");

        // Check that the given username and password combo exists
        $query = "SELECT *
                    FROM regularparticipant
                    WHERE username = '".$username."'
                    AND userpassword = '".$password."'
                ";

        $statement = oci_parse($db_conn, $query);
        $r = oci_execute($statement);
        $nrows = oci_fetch_all($statement, $res);
        console_log("$nrows rows fetched");

        //Credentials have been verified, so login & redirect to myprofile
        if ($nrows === 1) {
            $cookie_name = "username";
            $cookie_value = $username;
            setcookie($cookie_name, $cookie_value, time() + (86400), "/");
            header("Location: ./myprofile.php");
            exit;
        } else {
            echo "<script> alert('Incorrect Login Credentials.'); window.location.href='index.php'; </script>";
        }

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

    .transparent-box {
        background-color:rgba(0,0,0,.5);
        margin: auto;
        width: 550px;
        height:170px;
    }


    </style>

    <h1 style="color:white;
        font-weight:bold;
        text-align:center;
        font-size:400%;"> Fantasy Basketball </h1>

    <h1 style="color:white;
        text-align:center;
        font-size:100%;"> Developed by Kareem El-Wishahy, Victor Parangue, and Zach Vavasour </h1>


    <div class="transparent-box">
        <h2 style="color:white;
                text-align:center;
                font-weight:bold;
                font-size:150%;
                padding-top: 20px;"> Login </h2>

        <form action="" method="post" accept-charset="utf-8" style="
            padding-top: 20px;
            margin: auto;
            text-align:center;">

            <input type="text" name="username" placeholder="Username"></input>
            <input type="password" name="password" placeholder="Password"></input>
            <input type="submit" value="Login" style="background-color:#fc9803; color:white; border:none;"></input>
        </form>
    </div>
</html>