<?php
    if(isset($_POST['goods'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/aws_s3/S3Manager.php";
        

        $goods = json_decode($_POST['goods'],true);
        $user_id = $_SESSION['id'];
        $info = json_decode($_POST['info'],true);

        if($info['mode'] == 'create'){
            $goods_register_query = "INSERT INTO goods(name,category,price,details,user_id) VALUES('".$goods['name']."','".$goods['category']."',".$goods['price'].",'".$goods['details']."',$user_id)";
        
            if(mysqli_query($db_connect,$goods_register_query)){
                $goods_id = mysqli_insert_id($db_connect);

                $S3 = new S3Manager();
                $imageArray = $S3->reArrayFiles($_FILES['image']);
                $image_paths = $S3->uploadMulti("goods/",$imageArray);

                $image_query = "INSERT INTO goods_image(goods_id,image_path,is_main) VALUES";

                foreach($image_paths as $key=>$path){
                    $is_main = 0;
                    if($key == 0){
                        $is_main = 1;
                    }
                    $image_query .= "($goods_id,'$path',$is_main),";
                }

                $image_query = rtrim($image_query,',');

                mysqli_query($db_connect,$image_query);

                echo $goods_id;

                

            }


        }elseif($info['mode'] == 'update'){
            $goods_id = $info['goods_id'];
            $goods_update_query = "UPDATE goods SET name = '".$goods['name']."',category = '".$goods['category']."',price = ".$goods['price'].",details = '".$goods['details']."' 
            WHERE goods_id = $goods_id LIMIT 1";

            if(mysqli_query($db_connect,$goods_update_query)){
                $delete_image_query = "DELETE FROM goods_image WHERE goods_id = $goods_id";
                if(mysqli_query($db_connect,$delete_image_query)){
                    $S3 = new S3Manager();
                $imageArray = $S3->reArrayFiles($_FILES['image']);
                $image_paths = $S3->uploadMulti("goods/",$imageArray);

                $image_query = "INSERT INTO goods_image(goods_id,image_path,is_main) VALUES";

                foreach($image_paths as $key=>$path){
                    $is_main = 0;
                    if($key == 0){
                        $is_main = 1;
                    }
                    $image_query .= "($goods_id,'$path',$is_main),";
                }

                $image_query = rtrim($image_query,',');

                mysqli_query($db_connect,$image_query);

                echo $goods_id;

                }
            }




        }

        

        mysqli_close($db_connect);

    }else{
        echo -1;
    }

?>