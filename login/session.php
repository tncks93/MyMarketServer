<?php
    session_start();
    if(!isset($_SESSION['id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";
        $cookie = $_COOKIE['PHPSESSID'];
        $sess_query = "select user_id user where sess_id = '$cookie' limit 1";
        $sess_id = mysqli_fetch_assoc(mysqli_query($db_connect,$sess_query))['id'];
        if(isset($sess_id)){
            $_SESSION['id'] = $sess_id;
        }else{
            exit();
        }
    }
?>