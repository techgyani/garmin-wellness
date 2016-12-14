<?php
require_once '../vendor/autoload.php';


$server = new bhupendraMp\OAuth1\Client\Server\Garmin([
    'identifier' => getenv('consumerKey'),
    'secret' => getenv('consumerSecret'),
    'callback_uri' => getenv('callback_uri'),
]);
$serverAccessToken = "";
$serverSecret = "";
$userAccessToken = "db16742b-e4e6-4dd3-8263-a20d44ae909c";
$userSecret = "caCoRgiM3oX5UJoNfW7DDEH8l8wC91NaUa7";
$params = [
//TODO
];

$tempCred = new \League\OAuth1\Client\Credentials\TokenCredentials();
$tempCred->setIdentifier($userAccessToken);
$tempCred->setSecret($userSecret);
$activitySummary = $server->getActivitySummary($tempCred, $params);
var_dump($activitySummary);die;