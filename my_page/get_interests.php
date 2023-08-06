<?php
    if(isset($_GET['paging_idx'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $user_id = $_SESSION['id'];
        $paging_idx = $_GET['paging_idx'];

        $query_select_interests = "SELECT g.*,gi.image_path AS main_image,i.created_at AS saved_at FROM interest i JOIN goods g ON g.goods_id = i.goods_id JOIN goods_image gi ON (g.goods_id = gi.goods_id AND gi.is_main = 1) 
        WHERE i.user_id = $user_id %s ORDER BY i.created_at DESC LIMIT 10";

        $query_where_for_paging;
        if($paging_idx == "0"){
            $query_where_for_paging = "";
        }else{
            $query_where_for_paging = "AND i.created_at < '$paging_idx'";
        }

        $query_select_interests = sprintf($query_select_interests,$query_where_for_paging);

        if($result = mysqli_query($db_connect,$query_select_interests)){
            $list = array();
            while($row = mysqli_fetch_assoc($result)){
                $row['is_interest'] = true;
                $list[] = $row;
            }
            
            echo json_encode($list,JSON_UNESCAPED_UNICODE);

        }

        mysqli_close($db_connect);



    }else{
        exit();
    }



?>