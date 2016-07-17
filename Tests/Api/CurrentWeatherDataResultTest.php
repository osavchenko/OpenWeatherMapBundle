<?php

namespace Savchenko\Bundle\OpenWeatherMapBundle\Tests\Api;

use GuzzleHttp\Psr7\Response;
use Savchenko\Bundle\OpenWeatherMapBundle\Api\CurrentWeatherDataResult;

class CurrentWeatherDataResultTest extends \PHPUnit_Framework_TestCase
{
    use FakeWeatherDataTrait;

    private $response;

    public function setUp()
    {

        $this->response = $this->prophesize(Response::class);
    }

    /**
     * @test
     */
    public function It_should_save_the_response_when_constructed_from_response()
    {

        $this->response->getBody()->willReturn($this->getWeatherData());

        $sut = CurrentWeatherDataResult::fromApiResponse($this->response->reveal());
        $this->assertSame('London', $sut->name);
        $this->assertSame($this->response->reveal(), $sut->getResponse());

    }

    /**
     * @test
     */
    public function The_result_can_be_rendered_as_array()
    {

        $this->response->getBody()->willReturn($this->getWeatherData());

        $sut = CurrentWeatherDataResult::fromApiResponse($this->response->reveal());
        $this->assertSame($this->getWeatherData(), $sut->asJson());

    }

    /**
     * @test
     */
    public function The_result_can_be_rendered_as_json()
    {

        $this->response->getBody()->willReturn($this->getWeatherData());
        $sut = CurrentWeatherDataResult::fromApiResponse($this->response->reveal());
        $this->assertSame('London', $sut->asArray()['name']);

    }

}
