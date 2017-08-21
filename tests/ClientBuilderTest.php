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
}
