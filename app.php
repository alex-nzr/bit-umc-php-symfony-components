<?php
require_once realpath('./vendor/autoload.php');

use AlexNzr\BitUmcIntegration\Core\Application;

try
{
    $app = new Application();
    $response = $app->handle();
    /*$response->headers->set('Content-Type', 'application/json');
    $response->headers->set('Allow', 'GET, POST, OPTIONS, PUT, DELETE');
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
    $response->headers->set('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method');
    $response->headers->set('Access-Control-Allow-Credentials', true);*/
    $response->send();
}
catch (Exception $e)
{
    echo json_encode(["error" => $e->getMessage()]);
}