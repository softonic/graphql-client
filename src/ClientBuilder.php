<?php

namespace Softonic\GraphQL;

use League\OAuth2\Client\Provider\AbstractProvider as OAuth2Provider;
use Psr\Cache\CacheItemPoolInterface as Cache;

class ClientBuilder
{
    public static function build(string $endpoint, array $guzzleOptions = []): Client
    {
        $guzzleOptions = array_merge(['base_uri' => $endpoint], $guzzleOptions);

        return new Client(
            new \GuzzleHttp\Client($guzzleOptions),
            new ResponseBuilder(new DataObjectBuilder())
        );
    }

    public static function buildWithOAuth2Provider(
        string $endpoint,
        OAuth2Provider $oauthProvider,
        array $tokenOptions,
        Cache $cache,
        array $guzzleOptions = []
    ): Client {
        $guzzleOptions = array_merge(['base_uri' => $endpoint], $guzzleOptions);


        return new Client(
            \Softonic\OAuth2\Guzzle\Middleware\ClientBuilder::build(
                $oauthProvider,
                $tokenOptions,
                $cache,
                $guzzleOptions
            ),
            new ResponseBuilder(new DataObjectBuilder())
        );
    }
}
