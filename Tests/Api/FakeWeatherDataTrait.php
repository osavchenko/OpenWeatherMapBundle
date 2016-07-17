<?php

namespace Savchenko\Bundle\OpenWeatherMapBundle\Tests\Api;

trait FakeWeatherDataTrait
{

    /**
     * Get OpenWeatherMap response example
     *
     * @return string
     */
    protected function getWeatherData()
    {
        return json_encode(
            [
                'coord' => [
                    'lon' => -0.13,
                    'lat' => 51.51,
                ],
                'weather' => [
                    [
                        'id' => 800,
                        'main' => 'Clear',
                        'description' => 'clear sky',
                        'icon' => '01n',
                    ],
                ],
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
            ]
        );
    }
}
