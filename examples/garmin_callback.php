<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../vendor/autoload.php';

if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) && isset($_SESSION['temporary_credentials'])) {
    $server = new \techgyani\OAuth1\Client\Server\Garmin([
        'identifier' => getenv('consumerKey'),
        'secret' => getenv('consumerSecret'),
        'callback_uri' => getenv('callback_uri'),
    ]);

    // Retrieve the temporary credentials we saved before
    $temporaryCredentials = unserialize($_SESSION['temporary_credentials']);

    // We will now retrieve token credentials from the server

    $tokenCredentials = $server->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);
    $_SESSION['token_credentials'] = serialize($tokenCredentials); //saving user access token in session for test
    header('Location: garmin_api_test.php');
}
else {
    throw new Exception("Need temporary oauth credentials, oauth_token and oauth_verifier to proceed.");
}