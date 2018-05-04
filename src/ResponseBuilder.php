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
        
        if (!array_key_exists('data', $decodedResponse)) {
            $message = 'Invalid GraphQL JSON response.';
            if (array_key_exists('errors', $decodedResponse)
                && is_array($decodedResponse['errors'])
                && array_key_exists('message', $decodedResponse['errors'][0])) {
                $message .= ' ' . stripslashes($decodedResponse['errors'][0]['message']);
            }
            throw new \UnexpectedValueException($message);
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
            $message = 'Invalid JSON response.';
            if (json_last_error_msg()) {
                $message .= ' ' . json_last_error_msg();
            }
            throw new \UnexpectedValueException($message);
        }
        return $response;
    }
}
