<?php
    if(isset($_POST['goods_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $buyer_id = $_POST['buyer_id'];
        $goods_id = $_POST['goods_id'];

        $query_for_purchase= "INSERT INTO purchase(buyer_id,goods_id) VALUES($buyer_id,$goods_id)";

        $res;
        if(mysqli_query($db_connect,$query_for_purchase)){
            $res = "success";
            
        }else{
            $res = "failure";
            
        }

        echo json_encode($res,JSON_UNESCAPED_UNICODE);


    }else{
        exit();
    }

    // buyer_id,goods_id


?>