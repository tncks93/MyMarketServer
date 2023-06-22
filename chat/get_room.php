<?php
    if(isset($_GET['room_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $room_id = $_GET['room_id'];
        // $query_select_room = "SELECT "


    }else{
        exit();
    }


?>