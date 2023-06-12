<?php
    if(isset($_GET['page_idx'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $page_idx = $_GET['page_idx'];

        //g.*,gi.main,
        $get_goods_list_query = "SELECT g.*,gi.image_path AS main_image FROM goods g JOIN goods_image gi ON (g.goods_id = gi.goods_id AND gi.is_main = 1) 
        %s ORDER BY g.goods_id DESC LIMIT 10";


        if($page_idx == 0){
            $get_goods_list_query = sprintf($get_goods_list_query,"");
        }else{
            $where_for_paging = "WHERE g.goods_id < $page_idx";
            $get_goods_list_query = sprintf($get_goods_list_query,$where_for_paging);
        }

        if($result = mysqli_query($db_connect,$get_goods_list_query)){
            $goods_list = array();

            while($row = mysqli_fetch_assoc($result)){
                $goods_list[] = $row;
            }

            echo json_encode($goods_list,JSON_UNESCAPED_UNICODE);


        }else{
            echo mysqli_error($db_connect);
        }

        mysqli_close($db_connect);

    }else{
        exit();
    }


?>