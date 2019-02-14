<?php

namespace BlackrockM\ContentProviderClient\Provider;

use Http\Client\Common\HttpMethodsClient;

/**
 * Class ContentProviderService
 * @package BlackrockM\ContentProviderClient\Provider
 */
class ContentProviderService
{
    /** @var string */
    private $client;
    
    /**
     * ContentProviderService constructor.
     *
     * @param HttpMethodsClient $client
     */
    public function __construct(HttpMethodsClient $client)
    {
        $this->client = $client;
    }
    
    /**
     * @param RequestObject|null $requestObject
     *
     * @return string
     * @throws \Http\Client\Exception
     */
    public function execute(RequestObject $requestObject = null)
    {
        $response = $this->client->get(
            '/api/pages'.($requestObject ? '?'.array_filter($requestObject->toArray(), 'strlen') : [])
        );

        return $response->getBody()->getContents();
    }
}
