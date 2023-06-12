<?php
    if(isset($_POST['filter'])){
        require_once $_SERVER['DOCUMENT_ROOT']."/login/session.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/db/db.php";

        define("FILTER_NONE","none");

        $filter = json_decode($_POST['filter'],true);
        $page_idx = $_POST['page_idx'];

        $min_price = $filter['min_price'];
        $max_price = $filter['max_price'];

        $search_query = "SELECT g.*,gi.image_path AS main_image FROM goods g JOIN goods_image gi ON (g.goods_id = gi.goods_id AND gi.is_main = 1) 
        WHERE (g.price BETWEEN $min_price AND $max_price) %s %s ORDER BY g.goods_id DESC LIMIT 10";

        $where_condition;
        $where_paging;
        $category = $filter['category'];
        $keyword = preg_replace("/\s+/", "", $filter['keyword']);

        if($category == FILTER_NONE && $keyword == FILTER_NONE){
            $where_condition = "";

        }elseif($category == FILTER_NONE){
            $where_condition = "AND REPLACE(g.name,' ','') LIKE '%$keyword%'";
        }elseif($keyword == FILTER_NONE){
            $where_condition = "AND g.category = '$category'";
        }else{
            $where_condition = "AND g.category = '$category' AND REPLACE(g.name,' ','') LIKE '%$keyword%'";
        }

    
        if($page_idx == 0){
            $where_paging = "";
        }else{
            $where_paging = " AND g.goods_id < $page_idx";
        }

        $search_query = sprintf($search_query,$where_condition,$where_paging);


        if($result = mysqli_query($db_connect,$search_query)){
            $goods_list = array();
            while($row = mysqli_fetch_assoc($result)){
                $goods_list[] = $row;
            }

            echo json_encode($goods_list,JSON_UNESCAPED_UNICODE);
            
        }else{
            echo mysqli_error($db_connect);
        }







    }else{
        exit();
        
    }



?>