<?php

$get_goods_query = "SELECT g.*,u.name AS user_name,u.user_image,GROUP_CONCAT(gi.image_path SEPARATOR ',') AS goods_images FROM goods g join user u on g.user_id = u.user_id join goods_image gi on g.goods_id = gi.goods_id 
        WHERE g.goods_id = $goods_id";


?>