<?php

namespace Softonic\GraphQL\Test;

use PHPUnit\Framework\TestCase;
use Softonic\GraphQL\Client;

class ClientTest extends TestCase
{
    private $httpClient;
    private $mockGraphqlResponseBuilder;
    private $client;

    public function setUp()
    {
        $this->httpClient = $this->createMock(\GuzzleHttp\ClientInterface::class);
        $this->mockGraphqlResponseBuilder = $this->createMock(\Softonic\GraphQL\ResponseBuilder::class);
        $this->client = new Client($this->httpClient, $this->mockGraphqlResponseBuilder);
    }

    public function testSimpleQueryWhenHasNetworkErrors()
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willThrowException(new \GuzzleHttp\Exception\TransferException('library error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Network Error.');

        $query = $this->getSimpleQuery();
        $this->client->query($query);
    }

    public function testSimpleQueryWhenInvalidJsonIsReceived()
    {
        $query = $this->getSimpleQuery();

        $mockHttpResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willThrowException(new \UnexpectedValueException('Invalid JSON response.'));
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '',
                [
                    'connect_timeout' => 0,
                    'json' => [
                        'query' => $query,
                        'variables' => [],
                    ],
                ]
            )
            ->willReturn($mockHttpResponse);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid JSON response.');

        $this->client->query($query);
    }

    public function testSimpleQuery()
    {
        $mockResponse = $this->createMock(\Softonic\GraphQL\Response::class);
        $mockHttpResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);

        $response = [
            'data' => [
                'program' => [
                    'id_appstore' => null,
                ],
            ],
        ];
        $expectedData = $response['data'];
        $query = $this->getSimpleQuery();

        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willReturn($mockResponse);
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '',
                [
                    'connect_timeout' => 0,
                    'json' => [
                        'query' => $query,
                        'variables' => [],
                    ],
                ]
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->query($query);
        $this->assertInstanceOf(\Softonic\GraphQL\Response::class, $response);
    }

    public function testQueryWithVariables()
    {
        $mockResponse = $this->createMock(\Softonic\GraphQL\Response::class);
        $mockHttpResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);

        $response = [
            'data' => [
                'program' => [
                    'id_appstore' => null,
                ],
            ],
        ];

        $query = $this->getQueryWithVariables();
        $variables = [
            'idProgram' => '642e69c0-9b2e-11e6-9850-00163ed833e7',
            'locale'    => 'nl',
        ];

        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willReturn($mockResponse);
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '',
                [
                    'connect_timeout' => 0,
                    'json' => [
                        'query' => $query,
                        'variables' => $variables,
                    ],
                ]
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->query($query, $variables);
        $this->assertInstanceOf(\Softonic\GraphQL\Response::class, $response);
    }

    private function getSimpleQuery()
    {
        return <<<'QUERY'
{
  foo(id:"bar") {
    id_foo
  }
}
QUERY;
    }

    private function getQueryWithVariables()
    {
        return <<<'QUERY'
query GetFooBar($idFoo: String, $idBar: String) {
  foo(id: $idFoo) {
    id_foo
    bar (id: $idBar) {
      id_bar
    }
  }
}
QUERY;
    }
}
