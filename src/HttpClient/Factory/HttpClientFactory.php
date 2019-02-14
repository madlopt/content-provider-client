<?php

namespace BlackrockM\ContentProviderClient\HttpClient\Factory;

use BlackrockM\ContentProviderClient\HttpClient\Formatter\HttpClientFormatter;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\ContentTypePlugin;
use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\Authentication\Bearer;
use Psr\Log\LoggerInterface;

/**
 * Class HttpClientFactory
 * @package Blackrock\Proftit\Provider
 */
class HttpClientFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * HttpClientFactory constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string|null $apiDomain
     * @param string|null $jwt
     * @return HttpMethodsClient
     */
    public function createApiClient($apiDomain = null, $jwt = null)
    {
        $plugins = [
            new LoggerPlugin($this->logger, new HttpClientFormatter()),
            new ContentTypePlugin(),
        ];
    
        if ($apiDomain){
            $plugins[] = new AddHostPlugin(UriFactoryDiscovery::find()->createUri($apiDomain));
        }
        
        if ($jwt){
            $plugins[] = new AuthenticationPlugin(new Bearer($jwt));
        }
        
        $client = new PluginClient(
            HttpClientDiscovery::find(),
            $plugins
        );
        
        return new HttpMethodsClient($client, MessageFactoryDiscovery::find());
    }
}

