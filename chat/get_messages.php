<?php
    if(isset($_GET['room_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $room_id = $_GET['room_id'];
        $user_id = $_SESSION['id'];

        $query_select_messages = "SELECT c.*,(SELECT IF (c.to_id = $user_id,u.user_image,NULL) FROM user u WHERE u.user_id = c.from_id) AS op_image 
        FROM chat c WHERE c.room_id = $room_id ORDER BY c.chat_id;";

        if($result = mysqli_query($db_connect,$query_select_messages)){
            $messages = array();
            while($message = mysqli_fetch_assoc($result)){
                $messages[] = $message;
            }

            echo json_encode($messages,JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode($mysqli_error($db_connect),JSON_UNESCAPED_UNICODE);
        }


    }else{
        exit();
    }





?>