<?php

namespace Savchenko\Bundle\OpenWeatherMapBundle\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Savchenko\Bundle\OpenWeatherMapBundle\Api\CurrentWeatherData;

class CurrentWeatherDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $client;
    /**
     * @var CurrentWeatherData
     */
    protected $currentWeatherData;

    /**
     * Get OpenWeatherMap response example
     *
     * @return string
     */
    protected function getWeatherData()
    {
        return json_encode([
            'coord' => [
                'lon' => -0.13,
                'lat' => 51.51,
            ],
            'weather' => [[
                'id' => 800,
                'main' => 'Clear',
                'description' => 'clear sky',
                'icon' => '01n',
            ]],
            'main' => [
                'temp' => 282.94,
                'pressure' => 1007,
                'humidity' => 71,
                'temp_min' => 281.15,
                'temp_max' => 285.25,
            ],
            'wind' => [
                'speed' => 3.6,
                'deg' => 100,
            ],
            'clouds' => [
                'all' => 0,
            ],
            'dt' => 1460493742,
            'sys' => [
                'country' => 'GB',
                'sunrise' => 1460437723,
                'sunset' => 1460487258,
            ],
            'id' => 2643743,
            'name' => 'London',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->client = self::getMockBuilder('GuzzleHttp\Client')
            ->setMethods(['get'])
            ->getMock();

        $this->client->method('get')
            ->willReturn(new Response(200, [], $this->getWeatherData()));

        $this->currentWeatherData = new CurrentWeatherData('abc', $this->client);
    }

    /**
     * Override Guzzle get() method to throw exception
     *
     * @param string $message
     * @param int $statusCode
     * @throws \InvalidArgumentException
     */
    protected function overrideClientGetMethodAndSendRequest(string $message, int $statusCode)
    {
        $this->client->method('get')
            ->willThrowException(
                new ClientException(
                    $message,
                    new Request('GET', 'http://api.openweathermap.org/data/2.5/weather'),
                    new Response($statusCode)
                )
            );

        $this->currentWeatherData = new CurrentWeatherData('abc', $this->client);

        $this->currentWeatherData->loadByCityName('London');
    }

    /**
     * @expectedException \Savchenko\Bundle\OpenWeatherMapBundle\Exception\BadRequestException
     * @expectedExceptionMessage You doesn't set APPID parameter or it has incorrect value
     */
    public function testAppIdProblem()
    {
        $this->overrideClientGetMethodAndSendRequest(
            'Invalid API key. Please see http://openweathermap.org/faq#error401 for more info.',
            401
        );
    }

    /**
     * @expectedException \Savchenko\Bundle\OpenWeatherMapBundle\Exception\BadRequestException
     * @expectedExceptionMessage Data available only on commercial terms
     */
    public function testPaidFeatures()
    {
        $this->overrideClientGetMethodAndSendRequest('', 403);
    }

    /**
     * @expectedException \GuzzleHttp\Exception\ClientException
     */
    public function testCatchNonExpectedException()
    {
        $this->overrideClientGetMethodAndSendRequest('', 404);
    }

    public function testLoadByCityNameWithoutCountry()
    {
        $response = $this->currentWeatherData->loadByCityName('London');

        self::assertEquals('London', $response->name);
    }

    public function testLoadByCityNameWithCorrectCountryCode()
    {
        $response = $this->currentWeatherData->loadByCityName('London', 'GB');

        self::assertEquals('London', $response->name);
    }

    /**
     * @expectedException \Savchenko\Bundle\OpenWeatherMapBundle\Exception\InvalidCountryCodeException
     * @expectedExceptionMessage You should provide ISO 3166-1 alpha-2 country code
     */
    public function testLoadByCityNameWithIncorrectCountryCode()
    {
        $this->currentWeatherData->loadByCityName('London', 'UKR');
    }

    public function testLoadByCityId()
    {
        $response = $this->currentWeatherData->loadByCityId('2643743');

        self::assertEquals('London', $response->name);
    }

    public function testLoadByGeographicCoordinates()
    {
        $response = $this->currentWeatherData->loadByGeographicCoordinates(-0.13, 51.51);

        self::assertEquals('London', $response->name);
    }

    public function testLoadByZipCodeWithoutCountry()
    {
        $response = $this->currentWeatherData->loadByZipCode('EC1A1AH');

        self::assertEquals('London', $response->name);
    }

    public function testLoadByZipCodeWithCorrectCountryCode()
    {
        $response = $this->currentWeatherData->loadByZipCode('EC1A1AH', 'GB');

        self::assertEquals('London', $response->name);
    }

    /**
     * @expectedException \Savchenko\Bundle\OpenWeatherMapBundle\Exception\InvalidCountryCodeException
     * @expectedExceptionMessage You should provide ISO 3166-1 alpha-2 country code
     */
    public function testLoadByZipCodeWithIncorrectCountryCode()
    {
        $this->currentWeatherData->loadByZipCode('EC1A1AH', 'GBA');
    }
}
