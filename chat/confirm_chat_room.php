<?php
    if(isset($_POST['goods_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $user_id = $_SESSION['id'];
        $goods_id = $_POST['goods_id'];
        $seller_id = $_POST['seller_id'];
        
        $query_select_chat_room = "SELECT r.room_id FROM chat_room r WHERE (r.buyer_id = $user_id 
        AND r.goods_id= $goods_id AND r.seller_id = $seller_id) LIMIT 1";

        // echo json_encode($query_select_chat_room,JSON_UNESCAPED_UNICODE);/

        if($result = mysqli_query($db_connect,$query_select_chat_room)){
            $result = mysqli_fetch_assoc($result);

            $room_id;

            if($result == null){
                //기존 방 없음. 새 채팅방 만들기
                $created_at = $_POST['created_at'];
                $query_insert_new_chat_room = "INSERT INTO chat_room(goods_id,seller_id,buyer_id,updated_at) 
                VALUES($goods_id,$seller_id,$user_id,'$created_at')";

                mysqli_query($db_connect,$query_insert_new_chat_room);
                $room_id = mysqli_insert_id($db_connect);
            }else{
                //기존방
                $room_id = $result['room_id'];
            }

            echo json_encode($room_id,JSON_UNESCAPED_UNICODE);

            
        }else{

            echo json_encode(mysqli_error($db_connect),JSON_UNESCAPED_UNICODE);

        }
        

    }else{
        exit();
    }
    



?>