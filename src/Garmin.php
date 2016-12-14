<?php
 namespace bhupendraMp\OAuth1\Client\Server;

use League\Oauth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Server\User;
use League\OAuth1\Client\Credentials\TemporaryCredentials;
use GuzzleHttp\Exception\BadResponseException;
use League\OAuth1\Client\Credentials\CredentialsInterface;


class Garmin extends Server
{

    const API_URL = "https://connectapitest.garmin.com/";
    const USER_API_URL = "http://gcsapitest.garmin.com/wellness-api/rest/";

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
        return 'http://connecttest.garmin.com/oauthConfirm';
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
     * @param TemporaryCredentials|string
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
        $queryString = "oauth_token=".$temporaryIdentifier. "&oauth_callback=". $this->clientCredentials->getCallbackUri();

        return $this->buildUrl($url, $queryString);
    }

    /**
     * Retrieves token credentials by passing in the temporary credentials,
     * the temporary credentials identifier as passed back by the server
     * and finally the verifier code.
     *
     * @param TemporaryCredentials $temporaryCredentials
     * @param string               $temporaryIdentifier
     * @param string               $verifier
     *
     * @return TokenCredentials
     */
    public function getTokenCredentials(TemporaryCredentials $temporaryCredentials, $temporaryIdentifier, $verifier)
    {
        if ($temporaryIdentifier !== $temporaryCredentials->getIdentifier()) {
            throw new \InvalidArgumentException(
                'Temporary identifier passed back by server does not match that of stored temporary credentials.
                Potential man-in-the-middle.'
            );
        }

        $uri = $this->urlTokenCredentials();
        $bodyParameters = array('oauth_verifier' => $verifier);

        $client = $this->createHttpClient();

        $headers = $this->getHeaders($temporaryCredentials, 'POST', $uri, $bodyParameters);
        try {
            $response = $client->post($uri, [
                'headers' => $headers,
                'form_params' => $bodyParameters
            ]);
        } catch (BadResponseException $e) {
            return $this->handleTokenCredentialsBadResponse($e);
        }
        return $this->createTokenCredentials((string) $response->getBody());
    }

    protected function protocolHeader($method, $uri, CredentialsInterface $credentials, array $bodyParameters = array())
    {
        $parameters = array_merge(
            $this->baseProtocolParameters(),
            $this->additionalProtocolParameters(),
            array(
                'oauth_token' => $credentials->getIdentifier(),

            ),
            $bodyParameters
        );
        $this->signature->setCredentials($credentials);

        $parameters['oauth_signature'] = $this->signature->sign(
            $uri,
            array_merge($parameters, $bodyParameters),
            $method
        );

        return $this->normalizeProtocolParameters($parameters);
    }

    public function getActivitySummary(TokenCredentials $tokenCredentials, $params){
        $client = $this->createHttpClient();
        $query = '/activities?uploadStartTimeInSeconds=1452470400&uploadEndTimeInSeconds=1452556800';
        $headers = $this->getHeaders($tokenCredentials, 'GET', self::USER_API_URL.$query);
        try {
            $response = $client->get(self::USER_API_URL.$query, [
                'headers' => $headers
            ]);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $body = $response->getBody();
            $statusCode = $response->getStatusCode();

            throw new \Exception(
                "Received error [$body] with status code [$statusCode] when retrieving token credentials."
            );
        }
        return  $response->getBody()->getContents();
    }
    }