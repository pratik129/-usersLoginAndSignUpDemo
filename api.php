<?php

require_once('config.php');
require_once('user.php');

$user = new User($con);

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_SERVER['PATH_INFO'];

header('content-Type: application/json');

switch ($method) {
    case 'GET':
        if($endpoint == "/user") {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $user->getUser($data);
            echo json_encode($result);
        }
        break;
    case 'POST':
        if($endpoint == '/checklogin') {
            $d = file_get_contents('php://input');
            $data = json_decode($d);
            $result = $user->checkLogin($data);
            echo json_encode($result);
        } elseif ($endpoint == '/adduser') {
            $data = json_decode(file_get_contents('php://input'));
            // $data = $data[0];
            $result = $user->addUser($data);
            echo json_encode($result);
        }
}