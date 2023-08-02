<?php
    if(isset($_GET['state'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";
        
        //페이징 추가해야함
        $state = $_GET['state'];
        $query_select_sales = "SELECT g.*,gi.image_path AS main_image FROM goods g JOIN goods_image gi ON (g.goods_id = gi.goods_id AND gi.is_main = 1) 
        WHERE state = '$state' ORDER BY g.goods_id DESC LIMIT 10";

        if($result = mysqli_query($db_connect,$query_select_sales)){
            $list = array();
            while($row = mysqli_fetch_assoc($result)){
                $list[] = $row;
            }

            echo json_encode($list,JSON_UNESCAPED_UNICODE);
        }

        mysqli_close($db_connect);

    }else{
        exit();
    }


?>