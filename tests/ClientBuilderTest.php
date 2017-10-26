<?php

namespace Softonic\GraphQL\Test;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\ClientBuilder;

class ClientBuilderTest extends TestCase
{
    public function testBuild()
    {
        $client = ClientBuilder::build('http://foo.bar/qux');
        $this->assertInstanceOf(\Softonic\GraphQL\Client::class, $client);
    }

    public function testBuildWithOAuth2Provider()
    {
        $mockCache = $this->createMock(\Psr\Cache\CacheItemPoolInterface::class);
        $mockProvider = $this->createMock(\League\OAuth2\Client\Provider\AbstractProvider::class);
        $mockTokenOptions = [
            'grant_type' => 'client_credentials',
            'scope' => 'myscope',
        ];

        $client = ClientBuilder::buildWithOAuth2Provider(
            'http://foo.bar/qux',
            $mockProvider,
            $mockTokenOptions,
            $mockCache
        );
        $this->assertInstanceOf(\Softonic\GraphQL\Client::class, $client);
    }

    public function testBuildWithJwtAuth()
    {
        $mockAuthStrategy = $this->createMock(\Eljam\GuzzleJwt\Strategy\Auth\FormAuthStrategy::class);
        $mockTokenOptions = [
            'token_url' => '/api/token',
            'token_key' => 'access_token', // default is token
            'expire_key' => 'expires_in', // default is expires_in if not set
        ];

        $client = ClientBuilder::buildWithJwtAuth(
            'http://foo.bar/qux',
            $mockTokenOptions,
            $mockAuthStrategy
        );
        $this->assertInstanceOf(\Softonic\GraphQL\Client::class, $client);
    }
}
