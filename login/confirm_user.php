<?php
    require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

    $phone_num = $_POST['phoneNum'];


    $confirm_user_query = "SELECT EXISTS (SELECT * FROM user WHERE phone = '$phone_num') AS exist";

    if($result = mysqli_query($db_connect,$confirm_user_query)){
        $exist = mysqli_fetch_assoc($result)['exist'];

        if($exist == 1){
            echo json_encode("yes",JSON_UNESCAPED_UNICODE);


        }else{
            echo json_encode("no",JSON_UNESCAPED_UNICODE);
        }
    }else{
        echo mysqli_error($db_connect);
    }

    mysqli_close($db_connect);
    
    


?>