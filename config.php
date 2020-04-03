<?php
    //Connect to the database
    //This code is borrowed from https://www.students.cs.ubc.ca/~cs-304/resources/php-oracle-resources/oracle-test.txt
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example, 
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = oci_connect("ora_vicp24", "a43444447", "ora_vicp24/stu");
?>