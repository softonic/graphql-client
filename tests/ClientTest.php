<?php

namespace Softonic\GraphQL;

use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Softonic\GraphQL\DataObjects\Query\Item;
use UnexpectedValueException;

class ClientTest extends TestCase
{
    private MockObject $httpClient;

    private MockObject $mockGraphqlResponseBuilder;

    private Client $client;

    protected function setUp(): void
    {
        $this->httpClient                 = $this->createMock(ClientInterface::class);
        $this->mockGraphqlResponseBuilder = $this->createMock(ResponseBuilder::class);
        $this->client                     = new Client($this->httpClient, $this->mockGraphqlResponseBuilder);
    }

    public function testSimpleQueryWhenHasNetworkErrors(): void
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willThrowException(new TransferException('library error'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Network Error.');

        $query = $this->getSimpleQuery();
        $this->client->query($query);
    }

    private function getSimpleQuery(): string
    {
        return <<<'QUERY'
{
  foo(id:"bar") {
    id_foo
  }
}
QUERY;
    }

    public function testCanRetrievePreviousExceptionWhenSimpleQueryHasErrors(): void
    {
        $previousException = null;
        try {
            $originalException = new ServerException(
                'Server side error',
                $this->createMock(RequestInterface::class),
                $this->createMock(ResponseInterface::class)
            );

            $this->httpClient->expects($this->once())
                ->method('request')
                ->willThrowException($originalException);

            $query = $this->getSimpleQuery();
            $this->client->query($query);
        } catch (Exception $e) {
            $previousException = $e->getPrevious();
        } finally {
            $this->assertSame($originalException, $previousException);
        }
    }

    public function testSimpleQueryWhenInvalidJsonIsReceived(): void
    {
        $query = $this->getSimpleQuery();

        $mockHttpResponse = $this->createMock(ResponseInterface::class);
        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willThrowException(new UnexpectedValueException('Invalid JSON response.'));
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '',
                [
                    'body' => json_encode([
                        'query' => $query,
                    ], JSON_UNESCAPED_SLASHES),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn($mockHttpResponse);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid JSON response.');

        $this->client->query($query);
    }

    public function testSimpleQuery(): void
    {
        $mockResponse     = $this->createMock(Response::class);
        $mockHttpResponse = $this->createMock(ResponseInterface::class);

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
                    'body' => json_encode([
                        'query' => $query,
                    ], JSON_UNESCAPED_SLASHES),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->query($query);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testQueryWithVariables(): void
    {
        $mockResponse     = $this->createMock(Response::class);
        $mockHttpResponse = $this->createMock(ResponseInterface::class);

        $query     = $this->getQueryWithVariables();
        $variables = [
            'idFoo' => '642e69c0-9b2e-11e6-9850-00163ed833e7',
            'page'  => 1,
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
                    'body' => json_encode([
                        'query' => $query,
                        'variables' => $variables,
                    ], JSON_UNESCAPED_SLASHES),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->query($query, $variables);
        $this->assertInstanceOf(Response::class, $response);
    }

    private function getQueryWithVariables(): string
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

    public function testMutate(): void
    {
        $mockResponse     = $this->createMock(Response::class);
        $mockHttpResponse = $this->createMock(ResponseInterface::class);

        $query     = $this->getMutationQuery();
        $variables = Mutation::build([], new Item(['idFoo' => '642e69c0-9b2e-11e6-9850-00163ed833e7']));

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
                    'body' => json_encode([
                        'query' => $query,
                        'variables' => $variables,
                    ], JSON_UNESCAPED_SLASHES),
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->mutate($query, $variables);
        $this->assertInstanceOf(Response::class, $response);
    }

    private function getMutationQuery(): string
    {
        return <<<'QUERY'
mutation replaceFoo($foo: FooInput!) {
  replaceFoo(foo: $foo) {
    status
  }
}
QUERY;
    }
}
