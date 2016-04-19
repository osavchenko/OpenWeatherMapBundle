<?php

namespace Savchenko\Bundle\OpenWeatherMapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

/**
 * {@inheritdoc}
 */
class SavchenkoOpenWeatherMapExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $guzzleConfig = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/guzzle.yml'));
        $container->prependExtensionConfig('guzzle', $guzzleConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');
        
        $container->setParameter('savchenko.open_weather_map.api_key', $mergedConfig['api_key']);
    }
}
