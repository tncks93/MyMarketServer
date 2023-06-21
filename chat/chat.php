<?php
require "../vendor/autoload.php";

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

define('TYPE_LOBBY','LOBBY');
define('TYPE_MESSAGE','MESSAGE');
define('TYPE_ENTRY','ENTRY');


class Chat implements MessageComponentInterface {
    protected $db_connect;
    protected $clients;
    protected $rooms;
    protected $lobby;

    public function __construct($db_connection) {
        $this->db_connect = $db_connection;
        $this->clients = new SplObjectStorage();
        $this->rooms = array();
        $this->lobby = array();
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $conn->Chat = new stdClass();

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $msg = json_decode($msg,true);

        $from->Chat->type = $msg['type'];

        switch ($msg['type']) {
            case TYPE_LOBBY:
                echo "lobby";
                $user_id = $msg['user_id'];
                $from->Chat->user_id = $user_id;
                $this->lobby[$user_id] = $from;
                break;

            case TYPE_ENTRY:
                echo "entry";

                $room_id = $msg['room_id'];
                $user_id = $msg['user_id'];

                $from->Chat->room_id = $room_id;
                $from->Chat->user_id = $user_id;
                

                if(array_key_exists($room_id,$this->rooms)){
                    $this->rooms[$room_id]->members->attach($from);

                }else{
                    $query_entry_room = "SELECT seller_id,buyer_id FROM chat_room WHERE room_id = $room_id LIMIT 1";
                    $room = mysqli_fetch_assoc(mysqli_query($this->db_connect,$query_entry_room));
                    echo $room['buyer_id']." + ".$room['seller_id'];
                    
                    $this->rooms[$room_id] = new stdClass();
                    $this->rooms[$room_id]->buyer = $room['buyer_id'];
                    $this->rooms[$room_id]->seller = $room['seller_id'];
                    if($this->rooms[$room_id]->buyer == $user_id){
                        $from->Chat->to_id = $this->rooms[$room_id]->seller;
                    }elseif($this->rooms[$room_id]->seller == $user_id){
                        $from->Chat->to_id = $this->rooms[$room_id]->buyer;
                    }else{
                        echo "to_id error -> buyer : ".$this->rooms[$room_id]->buyer." seller : ".$this->rooms[$room_id]->seller
                        ." user : $user_id";
                    }
                    $this->rooms[$room_id]->members = new SplObjectStorage();
                    $this->rooms[$room_id]->members->attach($from);

                    $this->updateReadCount($this->db_connect,$room_id,$from->Chat->user_id);

                    if($this->rooms[$room_id]->members->count() == 2){
                        foreach ($this->rooms[$room_id]->members as $member) { 
                            if($member != $from){
                                $res = array();
                                $res['content_type'] = "read_all";
                                $member->send(json_encode($res,JSON_UNESCAPED_UNICODE));
                            }  
                        }
                    }

                }

               
                break;

            case TYPE_MESSAGE:
                echo "message";
                $room_id = $from->Chat->room_id;
                $from_id = $from->Chat->user_id;

                $message = $msg['message'];
                echo $msg['sent_at'];

                $to_id = $from->Chat->to_id;

                //접속자 수에 따라 is_read 설정
                if($this->rooms[$room_id]->members->count() == 2){
                    $is_read = 1;
                }else{
                    $is_read = 0;
                }

                $query_save_chat = "INSERT INTO chat(room_id,from_id,to_id,msg_type,content,sent_at,is_read) 
                VALUES($room_id,$from_id,$to_id,'".$msg['msg_type']."','$message','".$msg['sent_at']."',$is_read)";

                $query_update_room = "UPDATE chat_room SET is_activated = 1,updated_at = '".$msg['sent_at']."' WHERE room_id = $room_id LIMIT 1";

                if(mysqli_query($this->db_connect,$query_save_chat)){
                    
                    $chat_id = mysqli_insert_id($this->db_connect);
                    mysqli_query($this->db_connect,$query_update_room);
                    
                    $query_chat_select = "SELECT * FROM chat WHERE chat_id = $chat_id LIMIT 1";

                    $chat = mysqli_query($this->db_connect,$query_chat_select);
                    $chat = mysqli_fetch_assoc($chat);

                    echo json_encode($chat,JSON_UNESCAPED_UNICODE);

                    $res = array();
                    $res['content_type'] = "message";
                    $res['content'] = $chat;
                    $res = json_encode($res,JSON_UNESCAPED_UNICODE);

                    foreach ($this->rooms[$room_id]->members as $member) { 
                        if($member != $from){
                            $member->send($res);
                        }  
                    }
                    
                    //로비 업데이트
                    $count = $this->rooms[$room_id]->members->count();
                    echo "count : $count";

                    if($count < 2){
                        if(isset($this->lobby[$to_id])){
                            $query_get_room_last = "SELECT cr.room_id,u.name AS op_name,u.user_image AS op_image,cr.updated_at,c.content AS last_msg,c.msg_type AS last_msg_type,
                                IF (cr.seller_id = $from_id,gi.image_path,null) AS goods_image
                                FROM chat_room cr 
                                LEFT JOIN user u ON u.user_id = $from_id
                                LEFT JOIN goods_image gi ON (cr.goods_id = gi.goods_id AND gi.is_main = 1)
                                LEFT JOIN chat c ON c.chat_id = (SELECT chat_id FROM chat c2 WHERE c2.room_id = cr.room_id ORDER BY c2.chat_id DESC LIMIT 1) 
                                WHERE (cr.room_id = $room_id AND cr.is_activated = 1) LIMIT 1";

                            $last_room = mysqli_fetch_assoc(mysqli_query($this->db_connect,$query_get_room_last));

                            $this->lobby[$to_id]->send(json_encode($last_room,JSON_UNESCAPED_UNICODE));
                        }
                    }



                }

                

                break;
            
            default:
                break;
        }

    }

    public function onClose(ConnectionInterface $conn) {
        //로비 구현시 변경
        if($conn != null){
            if($conn->Chat->type == TYPE_LOBBY){
                $user_id = $conn->Chat->user_id;
                echo "lobby 퇴장 id : $user_id";
                unset($this->lobby[$user_id]);
            }else{
                $room_id = $conn->Chat->room_id;
                $this->rooms[$room_id]->members->detach($conn);
    
                if($this->rooms[$room_id]->members->count() == 0){
                    unset($this->rooms[$room_id]);
                    echo "room 삭제 id : $room_id";
                }
            }
        }
        

        
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    private function updateReadCount($db_connect,$room_id,$to_id){
        $query_update_unreads = "UPDATE chat SET is_read = 1 WHERE (room_id = $room_id && to_id = $to_id)";

        mysqli_query($db_connect,$query_update_unreads);
    }
}


?>