<?php
    if(isset($_GET['room_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $room_id = $_GET['room_id'];
        
        $user_id = $_SESSION['id'];

        $messages = array();
        $res = array();

        if(!isset($_GET['book_mark'])){

            //bookmark null 일 때
            $query_select_messages = "SELECT c.*,(SELECT IF (c.to_id = $user_id,u.user_image,NULL) FROM user u WHERE u.user_id = c.from_id) AS op_image
            FROM chat c WHERE c.room_id = $room_id ORDER BY c.chat_id DESC LIMIT 20";

            if($result = mysqli_query($db_connect,$query_select_messages)){
                while($message = mysqli_fetch_assoc($result)){
                    $messages[] = $message;
            }

            $messages = array_reverse($messages,false);
            $is_last = true;

            $res['messages'] = $messages;
            $res['is_last'] = $is_last;
            echo json_encode($res,JSON_UNESCAPED_UNICODE);


            }else{
                echo json_encode($db_connect,JSON_UNESCAPED_UNICODE);
            }



        }else{
            $book_mark = $_GET['book_mark'];

            $query_select_messages_front = "SELECT c.*,(SELECT IF (c.to_id = $user_id,u.user_image,NULL) FROM user u WHERE u.user_id = c.from_id) AS op_image
            FROM chat c WHERE (c.room_id = $room_id AND c.sent_at < '$book_mark') ORDER BY c.chat_id DESC LIMIT 10";

            $query_select_messages_back = "SELECT c.*,(SELECT IF (c.to_id = $user_id,u.user_image,NULL) FROM user u WHERE u.user_id = c.from_id) AS op_image
            FROM chat c WHERE (c.room_id = $room_id AND c.sent_at >= '$book_mark') ORDER BY c.chat_id LIMIT 10";

            $query_select_last_messages_id = "SELECT MAX(chat_id) AS last_id FROM chat WHERE room_id = $room_id";

            $result_front = mysqli_query($db_connect,$query_select_messages_front);
            $result_back = mysqli_query($db_connect,$query_select_messages_back);

            $messages_front = array();
            $messages_back = array();

            while($msg_front = mysqli_fetch_assoc($result_front)){
                $messages_front[] = $msg_front;
            }

            $messages_front = array_reverse($messages_front,false);

            while($msg_back = mysqli_fetch_assoc($result_back)){
                $messages_back[] = $msg_back;
            }

            $messages = array_merge($messages_front,$messages_back);

            $last_id = mysqli_fetch_assoc(mysqli_query($db_connect,$query_select_last_messages_id))['last_id'];
            $is_last;

            if($last_id == end($messages)['chat_id']){
                $is_last = true;
            }else{
                $is_last = false;
            }

            $res['messages'] = $messages;
            $res['is_last'] = $is_last;
            echo json_encode($res,JSON_UNESCAPED_UNICODE);
        }

        

        


    }else{
        exit();
    }





?>