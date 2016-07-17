<?php

namespace Savchenko\Bundle\OpenWeatherMapBundle\Api;

use Psr\Http\Message\ResponseInterface;

class CurrentWeatherDataResult
{
    private $data;

    /**
     * @var ResponseInterface
     */
    private $response;

    private function __construct($data, ResponseInterface $response = null)
    {
        $this->data = $data;

        if ($response) {
            $this->response = $response;
        }

    }

    /**
     * @param string $jsonString
     * @return CurrentWeatherDataResult
     */
    public static function fromJsonString($jsonString)
    {
        return new self(json_decode($jsonString));
    }

    public static function fromApiResponse(ResponseInterface $response)
    {
        return new self(json_decode($response->getBody()), $response);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        if ($this->response) {
            return $this->response;
        }
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return json_decode($this->asJson(), true);
    }

    /**
     * @return string
     */
    public function asJson()
    {
        return json_encode($this->data);
    }

    public function __get($name)
    {
        if (property_exists($this->data, $name)) {
            return $this->data->{$name};
        }

        throw new \InvalidArgumentException('Key not found in result');
    }
}