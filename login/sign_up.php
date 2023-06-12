<?php
    if(isset($_POST['user'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/aws_s3/S3Manager.php";

        $user = json_decode($_POST['user'],true);
        $phone = $user['phone'];
        $name = $user['name'];

        $sign_up_query = "INSERT INTO user(phone,name%s) VALUES('$phone','$name'%s)";
        $column;
        $values;

        if(isset($_FILES['image'])){
            $s3_path = "user/".$_FILES['image']['name'];
            $file_path = $_FILES['image']['tmp_name'];

            $S3 = new S3Manager();
            $image_url = $S3->uploadSingle($s3_path,$file_path);

            $column = ",user_image";
            $values = ",'$image_url'";
        }else{
            $column = "";
            $values = "";
        }

        $sign_up_query = sprintf($sign_up_query,$column,$values);

        if(mysqli_query($db_connect,$sign_up_query)){
            echo json_encode("success",JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(mysqli_error($db_connect),JSON_UNESCAPED_UNICODE);
        }

        mysqli_close($db_connect);



    }


?>