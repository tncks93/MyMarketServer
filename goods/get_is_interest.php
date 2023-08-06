<?php
    if(isset($_GET['goods_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $goods_id = $_GET['goods_id'];
        $user_id = $_SESSION['id'];

        $query_is_interest = "SELECT EXISTS 
        (SELECT id FROM interest WHERE user_id = $user_id AND goods_id = $goods_id LIMIT 1) as is_interest";

        if($result = mysqli_query($db_connect,$query_is_interest)){
            $is_interest = mysqli_fetch_assoc($result)['is_interest'];

            if($is_interest == 1){
                $is_interest = true;
            
            }else{
                $is_interest = false;
            }

            echo json_encode($is_interest,JSON_UNESCAPED_UNICODE);
            mysqli_close($db_connect);
        }



    }else{
        exit();
    }


?>