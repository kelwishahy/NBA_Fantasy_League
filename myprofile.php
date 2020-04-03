<?php
    if (empty($_COOKIE['username'])) {
        header("location: index.php");
        exit();
    }

    $username = $_COOKIE['username'];
    
    console_log("$username");


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

    <div class="transparent-overlay">
        <h1 style="color:white;
            font-weight:bold;
            text-align:center;
            font-size:400%;"> <?php echo $username; ?>'s Profile </h1>
        </div>
</html>

