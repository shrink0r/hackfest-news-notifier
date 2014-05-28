<?php

$base_dir = dirname(__DIR__);
require $base_dir . '/vendor/autoload.php';
use EsHackfest\Percolate\Server as PercolatServer;

$config_path = $base_dir . '/config/server.ini';
$config = parse_ini_file($config_path, true);

$pull_socket = $config['pull_socket'];
$push_socket = $config['web_socket'];

$server = new PercolatServer($pull_socket, $push_socket);
$server->run();
