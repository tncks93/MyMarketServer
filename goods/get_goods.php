<?php
    if(isset($_GET['goods_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $goods_id = $_GET['goods_id'];

        //goods.*,goods_image,user.name,user.user_image
        $get_goods_query = "SELECT g.*,u.name AS user_name,u.user_image,GROUP_CONCAT(gi.image_path SEPARATOR ',') AS goods_images FROM goods g join user u on g.user_id = u.user_id join goods_image gi on g.goods_id = gi.goods_id 
        WHERE g.goods_id = $goods_id";

        if($result = mysqli_query($db_connect,$get_goods_query)){
            
            $goods = array();
            $result = mysqli_fetch_assoc($result);
            if($result['goods_id'] == null){
                exit();
            }
            $result['seller']['user_id'] = $result['user_id'];
            $result['seller']['name'] = $result['user_name'];
            $result['seller']['user_image'] = $result['user_image'];
            unset($result['user_id']);
            unset($result['user_name']);
            unset($result['user_image']);
            $result['goods_images'] = explode(",",$result['goods_images']);

            echo json_encode($result,JSON_UNESCAPED_UNICODE);

        }else{
            echo mysqli_error($db_connect);
        }

        mysqli_close($db_connect);




    }else{
        exit();
    }



?>