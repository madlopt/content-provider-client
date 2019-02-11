<?php

namespace BlackrockM\ContentProviderClient\Factory;

use Symfony\Component\PropertyAccess\PropertyAccess;
use BlackrockM\ContentProviderClient\Handler\ContentProviderHandler;
use BlackrockM\ContentProviderClient\Provider\ContentProviderService;
use BlackrockM\ContentProviderClient\Utils\GeoIPService;

/**
 * Class ContentProviderHandlerFactory
 * @package BlackrockM\ContentProviderClient\Factory
 */
class ContentProviderHandlerFactory
{
    /**
     * @return ContentProviderHandler
     */
    public function create()
    {
        return new ContentProviderHandler(
            new ContentProviderService(
                getenv('CONTENT_PROVIDER_URI') . '/api/pages',
                getenv('CONTENT_PROVIDER_TOKEN')
            ),
            new GeoIPService(
                getenv('CONTENT_PROVIDER_GEOIP_URL'),
                getenv('CONTENT_PROVIDER_GEOIP_CLIENT'),
                getenv('CONTENT_PROVIDER_GEOIP_PASSWORD')
            )
        );
    }
}
