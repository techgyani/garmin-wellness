<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../vendor/autoload.php';


$server = new \techgyani\OAuth1\Client\Server\Garmin([
    'identifier' => getenv('consumerKey'),
    'secret' => getenv('consumerSecret'),
    'callback_uri' => getenv('callback_uri'),
]);


$params = [
    "uploadStartTimeInSeconds" => "1452470400",
    "uploadEndTimeInSeconds" => "1452556800"
];

$activitySummary = $server->getActivitySummary(unserialize($_SESSION['token_credentials']), $params);
print_r(json_decode($activitySummary));
