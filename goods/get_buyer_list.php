<?php
    if(isset($_GET['goods_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $goods_id = $_GET['goods_id'];
        
        $query_for_buyer = "SELECT u.user_id,u.name,u.user_image FROM user u JOIN chat_room cr ON (cr.goods_id = $goods_id AND cr.buyer_id = u.user_id) 
        ORDER BY cr.updated_at DESC";

        if($result = mysqli_query($db_connect,$query_for_buyer)){
            $buyer_list = array();
            while($buyer = mysqli_fetch_assoc($result)){
                $buyer_list[] = $buyer;
            }

            echo json_encode($buyer_list,JSON_UNESCAPED_UNICODE);
        }





        mysqli_close($db_connect);

    }else{
        exit();
    }



?>