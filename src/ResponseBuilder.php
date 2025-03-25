<?php

namespace Softonic\GraphQL;

use UnexpectedValueException;
use Psr\Http\Message\ResponseInterface;

class ResponseBuilder
{
    public function __construct(private ?DataObjectBuilder $dataObjectBuilder = null)
    {
    }

    public function build(ResponseInterface $httpResponse): Response
    {
        $body = $httpResponse->getBody();

        $normalizedResponse = $this->getNormalizedResponse($body);

        $dataObject = array_key_exists('dataObject', $normalizedResponse)
            ? $normalizedResponse['dataObject']
            : [];

        return new Response(
            $normalizedResponse['data'],
            $normalizedResponse['errors'],
            $dataObject
        );
    }

    private function getNormalizedResponse(string $body): array
    {
        $decodedResponse = $this->getJsonDecodedResponse($body);

        if (false === array_key_exists('data', $decodedResponse) && empty($decodedResponse['errors'])) {
            throw new UnexpectedValueException(
                'Invalid GraphQL JSON response. Response body: ' . json_encode($decodedResponse)
            );
        }

        $result = [
            'data'   => $decodedResponse['data'] ?? [],
            'errors' => $decodedResponse['errors'] ?? [],
        ];

        if (!is_null($this->dataObjectBuilder)) {
            $result['dataObject'] = $this->dataObjectBuilder->buildQuery($decodedResponse['data'] ?? []);
        }

        return $result;
    }

    private function getJsonDecodedResponse(string $body)
    {
        $response = json_decode($body, true);

        $error = json_last_error();
        if (JSON_ERROR_NONE !== $error) {
            throw new UnexpectedValueException(
                'Invalid JSON response. Response body: ' . $body
            );
        }

        return $response;
    }
}
