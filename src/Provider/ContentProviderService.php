<?php

namespace BlackrockM\ContentProviderClient\Provider;

use Http\Client\Common\HttpMethodsClient;
use Psr\Cache\CacheItemPoolInterface;
use function Blackrock\getenv;

/**
 * Class ContentProviderService
 * @package BlackrockM\ContentProviderClient\Provider
 */
class ContentProviderService
{
    /**
     * @var HttpMethodsClient
     */
    private $client;
    
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;
    
    /**
     * ContentProviderService constructor.
     *
     * @param HttpMethodsClient      $client
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct(HttpMethodsClient $client, CacheItemPoolInterface $cacheItemPool)
    {
        $this->client = $client;
        $this->cacheItemPool = $cacheItemPool;
    }
    
    /**
     * @param RequestObject|null $requestObject
     *
     * @return string
     * @throws \Http\Client\Exception
     */
    public function execute(RequestObject $requestObject = null)
    {
        $cacheItem = $this->cacheItemPool->getItem(
            md5(
                $requestObject ?
                    json_encode(array_filter($requestObject->toArray(), 'strlen')) :
                    'default'
            )
        );
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $response = $this->client->get(
            '/api/pages'.
            (
                $requestObject ?
                '?'.http_build_query(array_filter($requestObject->toArray(), 'strlen')) :
                ''
            )
        );
        
        $cacheExpires = (int)getenv('CONTENT_PROVIDER_CACHE_EXPIRE');
        $cacheItem->expiresAfter($cacheExpires > 0 ? $cacheExpires : 3600);
        
        $cacheItem->set((string)$response->getBody());
        $this->cacheItemPool->save($cacheItem);
        
        return $response->getBody();
    }
}
