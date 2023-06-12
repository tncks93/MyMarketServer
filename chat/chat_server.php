<?php

require "../chat/chat.php";
require "../db/db.php";

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require "../vendor/autoload.php";

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat($db_connect)
        )
    ),
    8080
);

$server->run();



?>