<?php
    if(isset($_GET['room_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $room_id= $_GET['room_id'];
        $get_goods_query = "SELECT g.goods_id,name,category,price,state,gi.image_path AS main_image 
        FROM chat_room cr JOIN goods g ON cr.goods_id = g.goods_id JOIN goods_image gi ON (g.goods_id = gi.goods_id AND gi.is_main=1) 
        WHERE cr.room_id = $room_id LIMIT 1";

        if($result = mysqli_query($db_connect,$get_goods_query)){
            $goods = mysqli_fetch_assoc($result);

            echo json_encode($goods,JSON_UNESCAPED_UNICODE);
        }

    }

        


?>  