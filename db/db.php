<?php
    include_once "/var/www/inc/dbinfo.inc";
   
    $db_connect = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD);
    mysqli_select_db($db_connect,DB_DATABASE);
    ?>


    