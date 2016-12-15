# garmin-wellness adapter for Oauth 1.0 Client.

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

Please set below two enviornment variables to run the examples.

consumerKey
consumerSecret
