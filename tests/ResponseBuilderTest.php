<?php

namespace Softonic\GraphQL;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

class ResponseBuilderTest extends TestCase
{
    private $dataObjectBuilder;

    private $responseBuilder;

    protected function setUp(): void
    {
        $this->dataObjectBuilder = $this->createMock(DataObjectBuilder::class);

        $this->responseBuilder = new ResponseBuilder($this->dataObjectBuilder);
    }

    public function testBuildMalformedResponse()
    {
        $mockHttpResponse = $this->createMock(ResponseInterface::class);
        $mockHttpResponse->expects($this->once())
            ->method('getBody')
            ->willReturn('malformed response');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid JSON response. Response body: ');

        $this->responseBuilder->build($mockHttpResponse);
    }

    public function buildInvalidGraphqlJsonResponseProvider()
    {
        return [
            'Invalid structure'    => [
                'body' => '["hola mundo"]',
            ],
            'No data in structure' => [
                'body' => '{"foo": "bar"}',
            ],
        ];
    }

    /**
     * @dataProvider buildInvalidGraphqlJsonResponseProvider
     */
    public function testBuildInvalidGraphqlJsonResponse(string $body)
    {
        $mockHttpResponse = $this->createMock(ResponseInterface::class);

        $mockHttpResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($body);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid GraphQL JSON response. Response body: ');

        $this->responseBuilder->build($mockHttpResponse);
    }

    public function testBuildValidGraphqlJsonWithoutErrors()
    {
        $mockHttpResponse = $this->createMock(ResponseInterface::class);

        $mockHttpResponse->expects($this->once())
            ->method('getBody')
            ->willReturn('{"data": {"foo": "bar"}}');

        $expectedData   = ['foo' => 'bar'];
        $dataObjectMock = [
            'query' => [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
        ];
        $this->dataObjectBuilder->expects($this->once())
            ->method('buildQuery')
            ->with($expectedData)
            ->willReturn($dataObjectMock);
        $response = $this->responseBuilder->build($mockHttpResponse);

        $this->assertEquals($expectedData, $response->getData());
        $this->assertEquals($dataObjectMock, $response->getDataObject());
    }

    public function buildValidGraphqlJsonWithErrorsProvider()
    {
        return [
            'Response with null data' => [
                'body' => '{"data": null, "errors": [{"foo": "bar"}]}',
            ],
            'Response without data'   => [
                'body' => '{"errors": [{"foo": "bar"}]}',
            ],
        ];
    }

    /**
     * @dataProvider buildValidGraphqlJsonWithErrorsProvider
     */
    public function testBuildValidGraphqlJsonWithErrors(string $body)
    {
        $mockHttpResponse = $this->createMock(ResponseInterface::class);

        $mockHttpResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($body);

        $this->dataObjectBuilder->expects($this->once())
            ->method('buildQuery')
            ->with([])
            ->willReturn([]);

        $response = $this->responseBuilder->build($mockHttpResponse);

        $this->assertEquals([], $response->getData());
        $this->assertEquals([], $response->getDataObject());
        $this->assertTrue($response->hasErrors());
        $this->assertEquals([['foo' => 'bar']], $response->getErrors());
    }
}
