<?php
    session_start();
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

        <form action="myprofile.php" method="post" style="
            padding-top: 20px;
            margin: auto;
            text-align:center;">

            <input type="text" name="username" placeholder="Username"></input>
            <input type="password" name="password" placeholder="Password"></input>
            <input type="submit" value="Login" style="background-color:#fc9803; color:white; border:none;"></input>
        </form>
    </div>
</html>

