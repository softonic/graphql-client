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
    private $dataObject;

    /**
     * @var array
     */
    private $errors;

    public function __construct(array $data, array $dataObject, array $errors = [])
    {
        $this->data       = $data;
        $this->dataObject = $dataObject;
        $this->errors     = $errors;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getDataObject(): array
    {
        return $this->dataObject;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
