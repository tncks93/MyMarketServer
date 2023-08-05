<?php
    if(isset($_GET['paging_idx'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $paging_idx = $_GET['paging_idx'];
        $user_id = $_SESSION['id'];

        //0 or else
        $query_select_purchase = "SELECT g.*,gi.image_path AS main_image FROM goods g JOIN goods_image gi ON (g.goods_id = gi.goods_id AND gi.is_main = 1) 
        JOIN purchase p ON (g.goods_id = p.goods_id AND p.buyer_id = $user_id)%s ORDER BY p.created_at DESC LIMIT 10";

        $query_where_paging = "";
        if($paging_idx!=0){
            $query_where_paging = " WHERE p.created_at < (SELECT created_at FROM purchase WHERE goods_id = $paging_idx LIMIT 1)";
        }

        $query_select_purchase = sprintf($query_select_purchase,$query_where_paging);

        if($result = mysqli_query($db_connect,$query_select_purchase)){
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