<?php

namespace BlackrockM\ContentProviderClient\Handler\Factory;

use BlackrockM\ContentProviderClient\Handler\ContentProviderHandler;
use BlackrockM\ContentProviderClient\HttpClient\Factory\HttpClientFactory;
use BlackrockM\ContentProviderClient\Provider\ContentProviderService;
use BlackrockM\ContentProviderClient\Utils\GeoIPService;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class ContentProviderHandlerFactory
 * @package BlackrockM\ContentProviderClient\Factory
 */
class ContentProviderHandlerFactory
{
    /**
     * @return ContentProviderHandler
     */
    public function create(LoggerInterface $logger = null)
    {
        return new ContentProviderHandler(
            new ContentProviderService(
                (new HttpClientFactory($logger ? $logger : new NullLogger()))
                    ->createApiClient(
                        getenv('CONTENT_PROVIDER_URI'),
                        getenv('CONTENT_PROVIDER_TOKEN')
                    )
            ),
            new GeoIPService(
                getenv('CONTENT_PROVIDER_GEOIP_URL'),
                getenv('CONTENT_PROVIDER_GEOIP_CLIENT'),
                getenv('CONTENT_PROVIDER_GEOIP_PASSWORD')
            )
        );
    }
}
