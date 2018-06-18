<?php

namespace Softonic\GraphQL;

use Psr\Http\Message\ResponseInterface;

class ResponseBuilder
{
    public function build(ResponseInterface $httpResponse)
    {
        $body = $httpResponse->getBody();

        $normalizedResponse = $this->getNormalizedResponse($body);

        return new Response($normalizedResponse['data'], $normalizedResponse['errors']);
    }

    private function getNormalizedResponse(string $body)
    {
        $decodedResponse = $this->getJsonDecodedResponse($body);

        if (false === isset($decodedResponse['data']) && false === isset($decodedResponse['errors'])) {
            throw new \UnexpectedValueException(
                'Invalid GraphQL JSON response. Response body: ' . print_r($decodedResponse, true)
            );
        }

        return [
            'data' => $decodedResponse['data'] ?? [],
            'errors' => $decodedResponse['errors'] ?? [],
        ];
    }

    private function getJsonDecodedResponse(string $body)
    {
        $response = json_decode($body, true);

        $error = json_last_error();
        if (JSON_ERROR_NONE !== $error) {
            throw new \UnexpectedValueException(
                'Invalid JSON response. Response body: ' . print_r($response, true)
            );
        }

        return $response;
    }
}
