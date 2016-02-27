<?php 
require_once('./private/config.php');
require_once('./private/api.php');
require_once('./private/api.php');
require_once('./libs/mustache.php');

header('Content-Type: application/javascript; charset:utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Expires: 0');
header('Pragma: no-cache');
header('Access-Control-Allow-Origin: *');

$methods = [];
$APIRFLX = new ReflectionClass("API");
$APIMethods = $APIRFLX->getMethods(ReflectionMethod::IS_PUBLIC);
foreach($APIMethods as $APIMethod)
{   
    $mname=$APIMethod->getName();
    if(mb_strpos($mname,"API_")===0)
    {
        $methods[]=[
            'method'=>mb_substr($mname,4),
            'parameters'=>implode(', ', array_map(function($param){return $param->getName();}, $APIMethod->getParameters()))
        ];
    }
}

$m = new Mustache_Engine(array(
    'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/tmplt')
));

echo $m->render('{{>client-js}}', $methods);