<?php
    if(isset($_POST['state'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $state = $_POST['state'];
        $goods_id = $_POST['goods_id'];

        $change_state_query = "UPDATE goods SET state = '$state' WHERE 
        goods_id = $goods_id LIMIT 1";

        if(mysqli_query($db_connect,$change_state_query)){
            echo json_encode("success",JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode("failure",JSON_UNESCAPED_UNICODE);
        }

        mysqli_close($db_connect);
    }else{
        exit();
    }

?>