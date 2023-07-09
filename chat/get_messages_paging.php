<?php
    if(isset($_GET['room_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        define('MODE_FRONT','front');
        define('MODE_BACK','back');

        $room_id = $_GET['room_id'];
        $paging_idx = $_GET['page_idx'];
        $paging_mode = $_GET['paging_mode'];
        $user_id = $_SESSION['id'];

        $messages = array();
        $res = array();
        
        $query_select_paging = "SELECT c.*,(SELECT IF (c.to_id = $user_id,u.user_image,NULL) FROM user u WHERE u.user_id = c.from_id) AS op_image
        FROM chat c WHERE (c.room_id = $room_id AND %s) ORDER BY c.chat_id%s LIMIT 10";

        switch($paging_mode){
            case MODE_FRONT : 
                $where_paging = "c.sent_at < '$paging_idx'";
                $query_select_paging = sprintf($query_select_paging,$where_paging," DESC");

                $result = mysqli_query($db_connect,$query_select_paging);
                while($message = mysqli_fetch_assoc($result)){
                    $messages[] = $message;
                }

                $messages = array_reverse($messages,false);

                $query_select_first_messages_id = "SELECT MIN(chat_id) AS first_id FROM chat WHERE room_id = $room_id";

                $first_id = mysqli_fetch_assoc(mysqli_query($db_connect,$query_select_first_messages_id))['first_id'];
                if(count($messages)>0){
                    if($messages[0]['chat_id'] == $first_id){
                        $res['is_first'] = true;
                    }else{
                        $res['is_first'] = false;
                    }
                }else{
                    $res['is_first'] = true;
                }
                

                break;

            case MODE_BACK : 
                $where_paging = "c.sent_at > '$paging_idx'";
                $query_select_paging = sprintf($query_select_paging,$where_paging,"");

                $query_select_last_messages_id = "SELECT MAX(chat_id) AS last_id FROM chat WHERE room_id = $room_id";

                $result = mysqli_query($db_connect,$query_select_paging);
                while($message = mysqli_fetch_assoc($result)){
                    $messages[] = $message;
                }

                $last_id = mysqli_fetch_assoc(mysqli_query($db_connect,$query_select_last_messages_id))['last_id'];

                if(end($messages)['chat_id'] == $last_id){
                    $res['is_last'] = true;
                }else{
                    $res['is_last'] = false;
                }

                break;

            default :
                exit();
                break;
        }

        $res['messages'] = $messages;

        echo json_encode($res,JSON_UNESCAPED_UNICODE);
    }else{
        exit();
    }



?>