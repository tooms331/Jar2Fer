<?php 
require_once('./private/config.php');
require_once('./private/api.php');

header('Content-Type: application/json; charset=utf-8'); 
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Expires: 0');
header('Pragma: no-cache');
header('Access-Control-Allow-Origin: *'); 

$commande = (string)$_REQUEST['commande'];
$params = $_REQUEST['params'];

$result=API::execute($commande,$params);
echo json_encode($result,JSON_UNESCAPED_UNICODE);