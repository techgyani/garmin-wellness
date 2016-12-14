<?php
session_start();
require_once '../vendor/autoload.php';


$server = new bhupendraMp\OAuth1\Client\Server\Garmin([
    'identifier'   => getenv('consumerKey'),
    'secret'       => getenv('consumerSecret'),
    'callback_uri' => getenv('callback_uri'),
]);


if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
    // Retrieve the temporary credentials we saved before
    $temporaryCredentials = unserialize($_SESSION['temporary_credentials']);

    // We will now retrieve token credentials from the server

    $tokenCredentials = $server->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);
    var_dump($tokenCredentials);
}
else {
    $temporaryCredentials = $server->getTemporaryCredentials();
    $_SESSION['temporary_credentials'] = serialize($temporaryCredentials);
    session_write_close();
// Second part of OAuth 1.0 authentication is to redirect the
// resource owner to the login screen on the server.
    $server->authorize($temporaryCredentials);

}
