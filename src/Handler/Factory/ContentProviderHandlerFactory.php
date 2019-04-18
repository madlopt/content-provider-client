<?php

namespace BlackrockM\ContentProviderClient\Handler\Factory;

use BlackrockM\ContentProviderClient\Handler\ContentProviderHandler;
use BlackrockM\ContentProviderClient\HttpClient\Factory\HttpClientFactory;
use BlackrockM\ContentProviderClient\Provider\ContentProviderService;
use BlackrockM\ContentProviderClient\Settings\ContentProviderSettings;
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
     * @var ContentProviderSettings
     */
    private $settings;

    /**
     * ContentProviderHandlerFactory constructor.
     * @param ContentProviderSettings $contentProviderSettings
     */
    public function __construct(ContentProviderSettings $contentProviderSettings = null)
    {
        $this->settings = $contentProviderSettings === null ? new ContentProviderSettings() : $contentProviderSettings;
    }

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
                        $this->settings->getUri(),
                        $this->settings->getToken()
                    ),
                $cacheItemPool
            ),
            GeoIpProviderFactory::create($logger, $cacheItemPool)
        );
    }
}
