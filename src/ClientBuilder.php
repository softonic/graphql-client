<?php

namespace Softonic\GraphQL;

use League\OAuth2\Client\Provider\AbstractProvider as OAuth2Provider;
use Psr\Cache\CacheItemPoolInterface as Cache;

class ClientBuilder
{
    public static function build(string $endpoint)
    {
        return new \Softonic\GraphQL\Client(
            new \GuzzleHttp\Client(['base_uri' => $endpoint]),
            new \Softonic\GraphQL\ResponseBuilder()
        );
    }

    public static function buildWithOAuth2Provider(
        string $endpoint,
        OAuth2Provider $oauthProvider,
        array $tokenOptions,
        Cache $cache
    ): Client {
        $guzzleOptions = [
            'base_uri' => $endpoint,
        ];

        return new \Softonic\GraphQL\Client(
            \Softonic\OAuth2\Guzzle\Middleware\ClientBuilder::build(
                $oauthProvider,
                $tokenOptions,
                $cache,
                $guzzleOptions
            ),
            new \Softonic\GraphQL\ResponseBuilder()
        );
    }
}
