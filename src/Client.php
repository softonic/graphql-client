<?php

namespace Softonic\GraphQL;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use Softonic\GraphQL\DataObjects\Mutation\MutationObject;

class Client
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var ResponseBuilder
     */
    private $responseBuilder;

    public function __construct(ClientInterface $httpClient, ResponseBuilder $responseBuilder)
    {
        $this->httpClient      = $httpClient;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * @throws \UnexpectedValueException When response body is not a valid json
     * @throws \RuntimeException         When there are transfer errors
     */
    public function query(string $query, ?array $variables = null): Response
    {
        return $this->executeQuery($query, $variables);
    }

    /**
     * @throws \UnexpectedValueException When response body is not a valid json
     * @throws \RuntimeException         When there are transfer errors
     */
    public function mutate(string $query, MutationObject $mutation): Response
    {
        return $this->executeQuery($query, $mutation);
    }

    private function executeQuery(string $query, $variables): Response
    {
        $body = ['query' => $query];
        if (!is_null($variables)) {
            $body['variables'] = $variables;
        }

        $options = [
            'body'    => json_encode($body, JSON_UNESCAPED_SLASHES),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];

        try {
            $response = $this->httpClient->request('POST', '', $options);
        } catch (TransferException $e) {
            throw new \RuntimeException('Network Error.' . $e->getMessage(), 0, $e);
        }

        return $this->responseBuilder->build($response);
    }
}
