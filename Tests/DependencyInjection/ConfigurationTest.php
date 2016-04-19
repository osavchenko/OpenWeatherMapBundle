<?php

namespace Savchenko\Bundle\OpenWeatherMapBundle\Tests\DependencyInjection;

use Savchenko\Bundle\OpenWeatherMapBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigTreeBuilder()
    {
        $config = [
            'savchenko_open_weather_map' => [
                'api_key' => 'some_api_key'
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(true), $config);

        self::assertEquals(array_merge($config['savchenko_open_weather_map']), $processedConfig);
    }
}