<?php

namespace Softonic\GraphQL;

use Psr\Http\Message\ResponseInterface;

class ResponseBuilder
{
    private $dataObjectBuilder;

    public function __construct(DataObjectBuilder $dataObjectBuilder)
    {
        $this->dataObjectBuilder = $dataObjectBuilder;
    }

    public function build(ResponseInterface $httpResponse)
    {
        $body = $httpResponse->getBody();

        $normalizedResponse = $this->getNormalizedResponse($body);

        return new Response(
            $normalizedResponse['data'],
            $normalizedResponse['dataObject'],
            $normalizedResponse['errors']
        );
    }

    private function getNormalizedResponse(string $body)
    {
        $decodedResponse = $this->getJsonDecodedResponse($body);

        if (false === array_key_exists('data', $decodedResponse) && empty($decodedResponse['errors'])) {
            throw new \UnexpectedValueException(
                'Invalid GraphQL JSON response. Response body: ' . json_encode($decodedResponse)
            );
        }

        return [
            'data'       => $decodedResponse['data'] ?? [],
            'dataObject' => $this->dataObjectBuilder->build($decodedResponse['data'] ?? []),
            'errors'     => $decodedResponse['errors'] ?? [],
        ];
    }

    private function getJsonDecodedResponse(string $body)
    {
        $response = json_decode($body, true);

        $error = json_last_error();
        if (JSON_ERROR_NONE !== $error) {
            throw new \UnexpectedValueException(
                'Invalid JSON response. Response body: ' . $body
            );
        }

        return $response;
    }
}
