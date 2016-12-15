<?php
session_start();
require_once '../vendor/autoload.php';


$server = new \techgyani\OAuth1\Client\Server\Garmin([
    'identifier' => getenv('consumerKey'),
    'secret' => getenv('consumerSecret'),
    'callback_uri' => getenv('callback_uri')
]);
//1st part fetching temporary credentials
$temporaryCredentials = $server->getTemporaryCredentials();
$_SESSION['temporary_credentials'] = serialize($temporaryCredentials);
session_write_close();
// Second part of OAuth 1.0 authentication is to redirect the resource owner to the login screen on the server.
$server->authorize($temporaryCredentials);
