<?php
    if(isset($_POST['id'])){
        session_start();
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";
        $sess_id = session_id();

        $id = $_POST['id'];
        $is_first = filter_var($_POST['isFirst'], FILTER_VALIDATE_BOOLEAN);

        if($is_first){
            $update_sess_query = "UPDATE user SET sess_id = '$sess_id' WHERE phone = '$id' LIMIT 1";
            mysqli_query($db_connect,$update_sess_query);

        }else{
            session_regenerate_id(false);
            $sess_id = session_id();
            $update_ses_query = "UPDATE user SET sess_id = '$sess_id' WHERE sess_id = '$id' LIMIT 1";
            mysqli_query($db_connect,$update_ses_query);
        }

        $sess_id = session_id();

        $get_user_query = "SELECT * FROM user WHERE sess_id = '$sess_id' LIMIT 1";

        if($result = mysqli_query($db_connect,$get_user_query)){
            $login_user = mysqli_fetch_assoc($result);
            if($login_user == null){
                exit();
            }
            $_SESSION['id'] = $login_user['user_id'];

            echo json_encode($login_user,JSON_UNESCAPED_UNICODE);


        }

        mysqli_close($db_connect);





    }



?>