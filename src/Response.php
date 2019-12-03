<?php

namespace Softonic\GraphQL;

class Response
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $errors;

    /**
     * @var array
     */
    private $dataObject;

    public function __construct(array $data, array $errors = [], array $dataObject = [])
    {
        $this->data       = $data;
        $this->errors     = $errors;
        $this->dataObject = $dataObject;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getDataObject(): array
    {
        return $this->dataObject;
    }
}
