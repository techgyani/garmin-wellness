# unofficial garmin-wellness adapter for Oauth 1.0 Client.

This package provides a Garmin API Client for the PHP League's [OAuth 1.0 Client](https://github.com/thephpleague/oauth1-client).

## Installation

```
composer require techgyani/garmin-wellness
```

## Usage

Usage is the same as The League's OAuth client, using `techgyani\OAuth1\Client\Server\Garmin` as the provider.

```php
$server = new techgyani\OAuth1\Client\Server\Garmin([
    'identifier'   => 'your-client-id',
    'secret'       => 'your-client-secret',
    'callback_uri' => 'http://callback.url/callback',
]);
```

Please refer to the Garmin wellness API for the available endpoints.

Below are the steps to test examples :
```
1. Copy examples in your web-server root if needed.
2. Define consumerKey, consumerSecret and callback_uri enviornment variables or alternatively set them directly in all php files in example. Below is the .htaccess code to demonstrate how you may do it.


setEnv consumerKey sddsadas
setEnv consumerSecret fdsfdsfsd
setEnv callback_uri http://localhost/garmin-wellness/vendor/techgyani/garmin-wellness/examples/garmin_callback.php

3. Now run examples/index.php in your browser. It should redirect you to the garmin server, where you need to login and authorize the application.
4. Once authorization is done user will be redirected to examples/garmin_api_test.php. There you must see activity summary output if everything is okay.
5. You can change parameter values in examples/garmin_api_test.php because it is picking up user token from the session.
```
