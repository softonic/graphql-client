<?php

namespace Softonic\GraphQL;

use GuzzleHttp\Cookie\CookieJar;
use League\OAuth2\Client\Provider\AbstractProvider;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;

class ClientBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $client = ClientBuilder::build('http://foo.bar/qux');
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testBuildWithGuzzleOptions(): void
    {
        $guzzleOptions = [
            'cookies' => new CookieJar(),
        ];

        $client = ClientBuilder::build('http://foo.bar/qux', $guzzleOptions);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testBuildWithOAuth2Provider(): void
    {
        $mockCache        = $this->createMock(CacheItemPoolInterface::class);
        $mockProvider     = $this->createMock(AbstractProvider::class);
        $mockTokenOptions = [
            'grant_type' => 'client_credentials',
            'scope'      => 'myscope',
        ];

        $client = ClientBuilder::buildWithOAuth2Provider(
            'http://foo.bar/qux',
            $mockProvider,
            $mockTokenOptions,
            $mockCache
        );
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testBuildWithOAuth2ProviderAndGuzzleOptions(): void
    {
        $mockCache        = $this->createMock(CacheItemPoolInterface::class);
        $mockProvider     = $this->createMock(AbstractProvider::class);
        $mockTokenOptions = [
            'grant_type' => 'client_credentials',
            'scope'      => 'myscope',
        ];

        $guzzleOptions = [
            'cookies' => new CookieJar(),
        ];

        $client = ClientBuilder::buildWithOAuth2Provider(
            'http://foo.bar/qux',
            $mockProvider,
            $mockTokenOptions,
            $mockCache,
            $guzzleOptions
        );
        $this->assertInstanceOf(Client::class, $client);
    }
}
