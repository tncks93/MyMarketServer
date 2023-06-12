<?php
    if(isset($_POST['goods_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $goods_id = $_POST['goods_id'];

        $delete_goods_query = "DELETE FROM goods WHERE goods_id = $goods_id LIMIT 1";

        if(mysqli_query($db_connect,$delete_goods_query)){
            echo json_encode("success",JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(mysqli_error($db_connect),JSON_UNESCAPED_UNICODE);
        }

        mysqli_close($db_connect);

    }else{
        exit();
    }



?>