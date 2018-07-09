<?php
/**
 * Created by PhpStorm.
 * User: faisal
 * Date: 6/4/18
 * Time: 12:25 AM
 */
require_once "steemconnect.php";
$steemconnect = new SteemConnect();
$userName = $steemconnect->getUser($steemconnect);
$response = [];
if (!empty($userName)){
    $response["success"] = true;
    $response["user"] = $userName;
    $response["token"] = $_SESSION["code"];
}else{
    $response["success"] = false;
    $response["user"] = "";
    $response["token"] = '';
}


print json_encode($response);