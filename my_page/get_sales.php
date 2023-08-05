<?php
    if(isset($_GET['state'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";
        
        //페이징 추가해야함
        $paging_idx = $_GET['paging_idx'];

        $state = $_GET['state'];
        $user_id = $_SESSION['id'];

        $query_select_sales = "SELECT g.*,gi.image_path AS main_image FROM goods g JOIN goods_image gi ON (g.goods_id = gi.goods_id AND gi.is_main = 1) 
        WHERE state = '$state' AND g.user_id = $user_id %s ORDER BY g.updated_at DESC LIMIT 10";

        $paging_query = "";
        if($paging_idx != 0){
            $paging_query = "AND g.updated_at < (SELECT updated_at FROM goods WHERE goods_id = $paging_idx LIMIT 1)";
        }

        $query_select_sales = sprintf($query_select_sales,$paging_query);
        
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