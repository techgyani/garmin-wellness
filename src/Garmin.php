<?php

namespace bhupendraMp\OAuth1\Client\Server;

use League\Oauth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Server\User;

class Garmin extends Server
{

    const API_URL = "https://connectapitest.garmin.com/";

    /**
     * Get the URL for retrieving temporary credentials.
     *
     * @return string
     */
    public function urlTemporaryCredentials()
    {
        return self::API_URL . 'oauth-service-1.0/oauth/request_token';
    }

    /**
     * Get the URL for redirecting the resource owner to authorize the client.
     *
     * @return string
     */
    public function urlAuthorization()
    {
        return 'https://connecttest.garmin.com/modern/oauthConfirm';
    }

    /**
     * Get the URL retrieving token credentials.
     *
     * @return string
     */
    public function urlTokenCredentials()
    {
        return self::API_URL . 'oauth-service-1.0/oauth/access_token';
    }

    /**
     * Get the URL for retrieving user details.
     *
     * @return string
     */
    public function urlUserDetails()
    {
        return self::API_URL . "users";
    }

    /**
     * Take the decoded data from the user details URL and convert
     * it to a User object.
     *
     * @param mixed $data
     * @param TokenCredentials $tokenCredentials
     *
     * @return User
     */
    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
        $user = new User;

        $arraySearchAndDestroy = function (array &$array, $key) {
            if (!array_key_exists($key, $array)) {
                return null;
            }

            $value = $array[$key];
            unset($array[$key]);
            return $value;
        };

        $user->uid = $arraySearchAndDestroy($data['user'], 'id');
        $user->nickname = $arraySearchAndDestroy($data['user'], 'username');
        $user->firstName = $arraySearchAndDestroy($data['user'], 'firstname');
        $user->lastName = $arraySearchAndDestroy($data['user'], 'lastname');
        $user->name = $arraySearchAndDestroy($data['user'], 'fullname');
        $user->email = $arraySearchAndDestroy($data['user'], 'email');
        $user->location = [
            'city'    => $arraySearchAndDestroy($data['user'], 'city'),
            'state'   => $arraySearchAndDestroy($data['user'], 'state'),
            'country' => $arraySearchAndDestroy($data['user'], 'country')
        ];
        $user->description = $arraySearchAndDestroy($data['user'], 'about');
        $user->imageUrl = $arraySearchAndDestroy($data['user'], 'userpic_https_url');
        $user->urls = $arraySearchAndDestroy($data['user'], 'domain');
        $user->extra = (array) $data['user'];

        return $user;
    }

    /**
     * Take the decoded data from the user details URL and extract
     * the user's UID.
     *
     * @param mixed $data
     * @param TokenCredentials $tokenCredentials
     *
     * @return string|int
     */
    public function userUid($data, TokenCredentials $tokenCredentials)
    {
        return $data['user']['id'];
    }

    /**
     * Take the decoded data from the user details URL and extract
     * the user's email.
     *
     * @param mixed $data
     * @param TokenCredentials $tokenCredentials
     *
     * @return string
     */
    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
        return $data['user']['email'];
    }

    /**
     * Take the decoded data from the user details URL and extract
     * the user's screen name.
     *
     * @param mixed $data
     * @param TokenCredentials $tokenCredentials
     *
     * @return string
     */
    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
        return $data['user']['username'];
    }

    /**
     * Get the authorization URL by passing in the temporary credentials
     * identifier or an object instance.
     *
     * @param TemporaryCredentials|string $temporaryIdentifier
     *
     * @return string
     */
    public function getAuthorizationUrl($temporaryIdentifier)
    {
        // Somebody can pass through an instance of temporary
        // credentials and we'll extract the identifier from there.
        if ($temporaryIdentifier instanceof TemporaryCredentials) {
            $temporaryIdentifier = $temporaryIdentifier->getIdentifier();
        }

        //$parameters = array('oauth_token' => $temporaryIdentifier, 'oauth_callback' => 'http://70.38.37.105:1225');

        $url = $this->urlAuthorization();
        //$queryString = http_build_query($parameters);
        $queryString = "oauth_token=".$temporaryIdentifier->getIdentifier(). "&oauth_callback=http://localhost/bhupendra/garmin-wellness/examples/garmin_client.php";

        return $this->buildUrl($url, $queryString);
    }
}