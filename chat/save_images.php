<?php
    if(isset($_POST['chats'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/aws_s3/S3Manager.php";

        $chats = json_decode($_POST['chats'],true);

        $S3 = new S3Manager();
        $files = $S3 -> reArrayFiles($_FILES['image']);
        $key_pre = "chat/";
        $image_paths = $S3->uploadMulti($key_pre,$files);

        for($i=0;$i<sizeof($chats);$i++){
            $chats[$i]['content'] = $image_paths[$i];
        }

        echo json_encode($chats,JSON_UNESCAPED_UNICODE);




    }else{
        exit();
    }



?>