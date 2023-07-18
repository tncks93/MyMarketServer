<?php
    if(isset($_POST['token'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $user_id = $_SESSION['id'];
        $token = $_POST['token'];

        $sql_insert_token = "INSERT INTO fcm_token(user_id,token) VALUES($user_id,'$token') ON DUPLICATE KEY UPDATE
        token = '$token'";

        if(mysqli_query($db_connect,$sql_insert_token)){
            echo json_encode("success",JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode("failure",JSON_UNESCAPED_UNICODE);
        }

    }else{
        exit();
    }



?>