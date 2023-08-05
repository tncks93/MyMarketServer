<?php
    if(isset($_POST['goods_id'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        $goods_id = $_POST['goods_id'];
        $buyer_id = $_SESSION['id'];

        $query_delete_purchase = "DELETE FROM purchase WHERE (buyer_id = $buyer_id AND goods_id = $goods_id) LIMIT 1";
        
        mysqli_query($db_connect,$query_delete_purchase);

        mysqli_close($db_connect);



    }else{
        exit();
    }
?>