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
    public function query(string $query, array $variables = null): Response
    {
        $options = [
            'json' => [
                'query' => $query,
            ],
        ];
        if (!is_null($variables)) {
            $options['json']['variables'] = $variables;
        }

        try {
            $response = $this->httpClient->request('POST', '', $options);
        } catch (TransferException $e) {
            throw new \RuntimeException('Network Error.' . $e->getMessage(), 0, $e);
        }

        return $this->responseBuilder->build($response);
    }

    public function uploadFile(string $query, array $variables = null, string $file, string $fileName): Response
    {
        $options = [
            'multipart' => 
              [
                [
                    'name'     => 'query',
                    'contents' => $query,

                ],
                [
                    'name'     => 'variables',
                    'contents' => json_encode($variables)
                ],
                [
                    'name'     => 'file',
                    'contents' => $file,
                    'filename' => $fileName,
                ]
              ]

        ];

        try {
            $response = $this->httpClient->request('POST', '', $options);
        } catch (TransferException $e) {
            throw new \RuntimeException('Network Error.' . $e->getMessage(), 0, $e);
        }

        return $this->responseBuilder->build($response);
    }
}
