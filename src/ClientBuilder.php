<?php

namespace Softonic\GraphQL;

use Eljam\GuzzleJwt\JwtMiddleware;
use Eljam\GuzzleJwt\Manager\JwtManager;
use Eljam\GuzzleJwt\Strategy\Auth\AuthStrategyInterface;
use GuzzleHttp\HandlerStack;
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

    public static function buildWithJwtAuth(
        string $baseEndpoint,
        array $tokenOptions,
        AuthStrategyInterface $authStrategy
    ): Client {
        $guzzleOptions = [
            'base_uri' => $baseEndpoint,
        ];
        $jwtManager = new JwtManager(
            new \GuzzleHttp\Client($guzzleOptions),
            $authStrategy,
            $tokenOptions
        );
        $stack = HandlerStack::create();
        $stack->push(new JwtMiddleware($jwtManager));
        return new \Softonic\GraphQL\Client(
            new \GuzzleHttp\Client([
                'handler' => $stack,
                'base_uri' => $baseEndpoint
            ]),
            new \Softonic\GraphQL\ResponseBuilder()
        );
    }
}
