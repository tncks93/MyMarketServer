<?php
    if(isset($_GET['room_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $id = $_SESSION['id'];
        $room_id = $_GET['room_id'];

        $query_select_all_unread = "SELECT count(*) AS unreads FROM chat WHERE (is_read = 0 && to_id = $id && room_id = $room_id)";

        if($result = mysqli_query($db_connect,$query_select_all_unread)){
            $count = mysqli_fetch_assoc($result)['unreads'];

            echo $count;
        }else{
            echo 0;
        }

    }else{
        exit();
    }
    



?>