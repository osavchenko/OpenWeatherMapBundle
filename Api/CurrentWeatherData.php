<?php

namespace Savchenko\Bundle\OpenWeatherMapBundle\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Savchenko\Bundle\OpenWeatherMapBundle\Exception\BadRequestException;
use Savchenko\Bundle\OpenWeatherMapBundle\Exception\InvalidCountryCodeException;
use Symfony\Component\Intl\Intl;

/**
 * Get current weather data information
 *
 * @author Oleksandr Savchenko
 */
class CurrentWeatherData
{
    /**
     * @var string OpenWeatherMap API key
     */
    private $apiKey;

    /**
     * @var Client
     */
    private $client;

    public function __construct(string $apiKey, Client $client)
    {
        $this->apiKey = $apiKey;
        $this->client = $client;
    }

    /**
     * Send request to OpenWeatherMap API endpoint
     *
     * @param array $params query parameters
     * @return ResponseInterface
     * @throws BadRequestException
     * @throws GuzzleException
     */
    private function sendRequest(array $params) : ResponseInterface {
        $params = array_merge(['appid' => $this->apiKey], $params);

        try {
            $response = $this->client->get('', [
                'query' => $params,
            ]);
        }
        catch (ClientException $exception) {
            switch ($exception->getCode()) {
                case 401:
                    throw new BadRequestException('You doesn\'t set APPID parameter or it has incorrect value');
                case 403:
                    throw new BadRequestException('Data available only on commercial terms');
                default:
                    throw $exception;
            }
        }

        return $response;
    }

    /**
     * Validate country code
     *
     * @param string $countryCode country code in ISO 3166-1 alpha-2 format
     * @return bool
     * @throws InvalidCountryCodeException
     */
    private function validateCountryCode(string $countryCode) :bool
    {
        if (Intl::getRegionBundle()->getCountryName($countryCode) === null) {
            throw new InvalidCountryCodeException('You should provide ISO 3166-1 alpha-2 country code');
        }

        return true;
    }

    /**
     * Call by city name or city name and country code
     *
     * @param string $cityName
     * @param string $countryCode optional country code in ISO 3166-1 alpha-2 format
     * @return \stdClass
     * @throws GuzzleException on request exception
     * @throws BadRequestException
     * @throws InvalidCountryCodeException on incorrect country code
     */
    public function loadByCityName(string $cityName, string $countryCode = null) : \stdClass
    {
        return json_decode(
            $this->sendRequest(['q' => $this->addCountryCodeToBase($cityName, $countryCode)])->getBody()
        );
    }

    /**
     * Call by city id
     *
     * @param string $cityCode
     * @return \stdClass
     * @throws GuzzleException on request exception
     * @throws BadRequestException
     */
    public function loadByCityId(string $cityCode) : \stdClass
    {
        return json_decode($this->sendRequest(['id' => $cityCode])->getBody());
    }

    /**
     * Call by geographic coordinates
     *
     * @param float $lat location latitude 
     * @param float $lon location longitude
     * @return \stdClass
     * @throws GuzzleException on request exception
     * @throws BadRequestException
     */
    public function loadByGeographicCoordinates(float $lat, float $lon) : \stdClass
    {
        return json_decode($this->sendRequest(['lat' => $lat, 'lon' => $lon])->getBody());
    }

    /**
     * Call by city name or city name and country code
     *
     * @param string $zipCode zip code
     * @param string $countryCode optional country code in ISO 3166-1 alpha-2 format
     * @return \stdClass
     * @throws GuzzleException on request exception
     * @throws BadRequestException
     * @throws InvalidCountryCodeException on incorrect country code
     */
    public function loadByZipCode(string $zipCode, string $countryCode = null) : \stdClass
    {
        return json_decode(
            $this->sendRequest(['zip' => $this->addCountryCodeToBase($zipCode, $countryCode)])->getBody()
        );
    }

    /**
     * Add country code to city name or zip code
     *
     * @param string $base
     * @param string|null $countryCode
     * @return string
     * @throws InvalidCountryCodeException
     */
    private function addCountryCodeToBase(string $base, string $countryCode = null) : string
    {
        if ($countryCode !== null) {
            $this->validateCountryCode($countryCode);

            $base .= ',' . $countryCode;
        }

        return $base;
    }
}