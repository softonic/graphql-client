<?php

namespace Softonic\GraphQL;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;

class Client
{
    private $httpClient;
    private $responseBuilder;

    public function __construct(ClientInterface $httpClient, ResponseBuilder $responseBuilder)
    {
        $this->httpClient = $httpClient;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * @throws \UnexpectedValueException When response body is not a valid json
     * @throws \RuntimeException         When there are transfer errors
     */
    public function query(string $query, array $variables = []): Response
    {
        $options = [
            'json' => [
                'query' => $query,
                'variables' => json_encode($variables),
            ],
        ];

        try {
            $response = $this->httpClient->request('POST', '', $options);
        } catch (TransferException $e) {
            throw new \RuntimeException('Network Error.');
        }

        return $this->responseBuilder->build($response);
    }
}
