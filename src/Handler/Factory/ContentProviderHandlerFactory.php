<?php

namespace BlackrockM\ContentProviderClient\Handler\Factory;

use BlackrockM\ContentProviderClient\Handler\ContentProviderHandler;
use BlackrockM\ContentProviderClient\HttpClient\Factory\HttpClientFactory;
use BlackrockM\ContentProviderClient\Provider\ContentProviderService;
use BlackrockM\ContentProviderClient\Settings\ContentProviderHandlerSettings;
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
     * @var ContentProviderHandlerSettings
     */
    private $settings;

    /**
     * ContentProviderHandlerFactory constructor.
     */
    public function __construct($contentProviderUri = false, $contentProviderToken = false)
    {
        $this->settings = new ContentProviderHandlerSettings(
            $contentProviderUri ? $contentProviderUri : getenv('CONTENT_PROVIDER_URI'),
            $contentProviderToken? $contentProviderToken : getenv('CONTENT_PROVIDER_TOKEN')
        );
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
                        $this->settings->getContentProviderUri(),
                        $this->settings->getContentProviderToken()
                    ),
                $cacheItemPool
            ),
            GeoIpProviderFactory::create($logger, $cacheItemPool)
        );
    }
}
