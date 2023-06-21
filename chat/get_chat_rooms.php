<?php
    if(isset($_GET['mode'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        define('MODE_FIRST','first');
        define('MODE_EARLIER','earlier');
        define('MODE_LATER','later');

        $id = $_SESSION['id'];
        
        $page_idx = $_GET['page_idx'];
        $mode = $_GET['mode'];
        $limit = 10;

        $query_get_rooms = "SELECT cr.room_id,u.name AS op_name,u.user_image AS op_image,cr.updated_at,c.content AS last_msg,c.msg_type AS last_msg_type,
	        IF (cr.buyer_id = $id,gi.image_path,null) AS goods_image
            FROM chat_room cr 
            LEFT JOIN user u ON u.user_id = 
                (CASE
                    WHEN cr.seller_id = $id THEN cr.buyer_id
                    WHEN cr.buyer_id = $id THEN cr.seller_id
                    END)
            LEFT JOIN goods_image gi ON (cr.goods_id = gi.goods_id AND gi.is_main = 1)
            LEFT JOIN chat c ON c.chat_id = (SELECT chat_id FROM chat c2 WHERE c2.room_id = cr.room_id ORDER BY c2.chat_id DESC LIMIT 1) 
            WHERE ((cr.seller_id = $id OR cr.buyer_id = $id) AND cr.is_activated = 1 %s) ORDER BY cr.updated_at DESC%s";

        $paging_query = "";
        $limit_query = "";

        switch($mode){
            case MODE_FIRST:
                $limit_query = " LIMIT $limit";
                break;

            case MODE_EARLIER:
                $paging_query = "AND cr.updated_at < '$page_idx'";
                $limit_query = " LIMIT $limit";
                break;

            case MODE_LATER:
                $paging_query = "AND cr.updated_at > '$page_idx'";
                break;
        }

        // if($page_idx == "0"){
            
        // }else{
            
        // }

        $query_get_rooms = sprintf($query_get_rooms,$paging_query,$limit_query);

        if($result = mysqli_query($db_connect,$query_get_rooms)){
            $rooms_list = array();
            while($row = mysqli_fetch_assoc($result)){
                $rooms_list[] = $row;
                
            }
            echo json_encode($rooms_list,JSON_UNESCAPED_UNICODE);
        }else{
            json_encode(mysqli_error($db_connect),JSON_UNESCAPED_UNICODE);
        }



    }else{
        exit();
    }


?>