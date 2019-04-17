<?php

namespace BlackrockM\ContentProviderClient\Handler\Factory;

use BlackrockM\ContentProviderClient\Handler\ContentProviderHandler;
use BlackrockM\ContentProviderClient\HttpClient\Factory\HttpClientFactory;
use BlackrockM\ContentProviderClient\Provider\ContentProviderService;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use BlackrockM\GeoIp\Client\Provider\Factory\GeoIpProviderFactory;
use Symfony\Component\Cache\Adapter\NullAdapter;
use function Blackrock\getenv;

/**
 * Class ContentProviderHandlerFactory
 * @package BlackrockM\ContentProviderClient\Factory
 */
class ContentProviderHandlerFactory
{
    /**
     * @return ContentProviderHandler
     */
    public function create(LoggerInterface $logger = null, CacheItemPoolInterface $cacheItemPool = null)
    {
        $logger = $logger !== null ? $logger : new NullLogger();
        $cacheItemPool = $cacheItemPool === null ? new NullAdapter() : $cacheItemPool;

        return new ContentProviderHandler(
            $logger,
            new ContentProviderService(
                (new HttpClientFactory($logger))
                    ->createApiClient(
                        getenv('CONTENT_PROVIDER_URI'),
                        getenv('CONTENT_PROVIDER_TOKEN')
                    ),
                $cacheItemPool
            ),
            GeoIpProviderFactory::create($logger, $cacheItemPool)
        );
    }
}
