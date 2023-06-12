<?php
    if(isset($_POST['user'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/aws_s3/S3Manager.php";

        $user_name = json_decode($_POST['user'],true)['name'];
        $user_id = $_SESSION['id'];

        $update_user_query = "UPDATE user SET name = '$user_name'%s WHERE user_id = $user_id LIMIT 1";
        $image_column;
    

        if(isset($_FILES['image'])){
            $s3_path = "user/".$_FILES['image']['name'];
            $file_path = $_FILES['image']['tmp_name'];

            $S3 = new S3Manager();
            $image_url = $S3->uploadSingle($s3_path,$file_path);

            $image_column = ", user_image = '$image_url'";
        }else{
            $image_column = ", user_image = DEFAULT(user_image)";
        
        }

        $update_user_query = sprintf($update_user_query,$image_column);

        // echo json_encode($update_user_query,JSON_UNESCAPED_UNICODE);

        if(mysqli_query($db_connect,$update_user_query)){
            echo json_encode("success",JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(mysqli_error($db_connect),JSON_UNESCAPED_UNICODE);
        }

        mysqli_close($db_connect);

    }



?>