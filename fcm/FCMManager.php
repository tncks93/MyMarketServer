<?php
    require_once "/var/www/inc/fcminfo.inc";

    class FCMManager {
        private $url;
        private $auth_key;

        private $to;
        private $body;

        public function __construct($to,$body){

            $this->auth_key = ACCESS_KEY;
            $this->url = API_URL;

            $this->to = $to;
            $this->body = $body;
        }

        public function sendFCM(){

            $data = array();
            $header = array("Content-Type:application/json","Authorization:Key=".$this -> auth_key);
            $data['to'] = $this->to;
            $data['priority'] = "high";
            $data['data'] = $this->body;

            $json = json_encode($data,JSON_UNESCAPED_UNICODE);
            echo $json;
        

            $ch = curl_init($this->url);
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
            curl_setopt($ch,CURLOPT_HEADER,true);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $result = curl_exec($ch);
            echo $result;

        }
    }




?>