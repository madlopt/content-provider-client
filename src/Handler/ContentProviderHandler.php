<?php

namespace BlackrockM\ContentProviderClient\Handler;

use BlackrockM\ContentProviderClient\Provider\RequestObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use BlackrockM\ContentProviderClient\Utils\GeoIPService;
use BlackrockM\ContentProviderClient\Provider\ContentProviderService;

/**
 * Class ContentProviderHandler
 * @package BlackrockM\ContentProviderClient\Handler
 */
class ContentProviderHandler
{
    /**
     * @var ContentProviderService
     */
    private $contentProviderService;
    
    /**
     * @var GeoIPService
     */
    private $geoIPService;
    
    /**
     * @var string|null
     */
    private $defaultPage = null;
    
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * ContentProviderHandler constructor.
     *
     * @param LoggerInterface        $logger
     * @param ContentProviderService $contentProviderService
     * @param GeoIPService           $geoIPService
     */
    public function __construct(
        LoggerInterface $logger,
        ContentProviderService $contentProviderService,
        GeoIPService $geoIPService
    ) {
        $this->logger = $logger;
        $this->contentProviderService = $contentProviderService;
        $this->geoIPService = $geoIPService;
    }
    
    /**
     * Get default `page` from API server
     *
     * @return string|null
     */
    private function getDefaultPage()
    {
        if ($this->defaultPage === null) {
            $response = $this->contentProviderService->execute();
            $decoded = \json_decode($response, true);
            
            if (is_array($decoded) && isset($decoded['list'])) {
                foreach ($decoded['list'] as $page) {
                    if ($page['country_code'] === null) {
                        return $this->defaultPage = $page;
                    }
                }
            }
        }
        
        throw new \UnexpectedValueException('Default page doesn\'t find out.');
    }
    
    /**
     * Take value from `page`
     *
     * @param string $path
     * @param array $item
     * @return mixed
     */
    private function pathAccessor($path, $item)
    {
        $path = preg_replace_callback('/(\.?[^.]+\.?)/', function ($matches) {
            return '[' . trim($matches[0], '.') . ']';
        }, $path);
        
        $accessor = PropertyAccess::createPropertyAccessor();
        
        if (preg_match('/^\[list\]\[([0-9]+)\](.*)$/', $path, $matches) != 0) {
            return $accessor->getValue($item, $matches[2]);
        }
        
        return $accessor->getValue($item, $path);
    }
    
    
    
    /**
     * Allowed params are:
     * ```
     * path
     * country_auto_resolve
     * name
     * country_code
     * ```
     *
     * @param array $attrs shortcode attributes
     * @return string|null
     */
    public function execute($attrs = [])
    {
        try {
            
            if ((!isset($attrs['country_auto_resolve']) || $attrs['country_auto_resolve'] === 'true') &&
                empty($attrs['country_code'])) {
                
                $geoData = $this->geoIPService->getGeoData();
                $attrs['country_code'] = $geoData['country_code'];
            }
            
            $requestAttrs = array_intersect_key($attrs, array_flip(['name', 'country_code']));
            
            $response = $this->contentProviderService->execute(RequestObject::createFromArray($requestAttrs));
            $decoded = \json_decode($response, true);
            $page = null;
            
            if (!empty($attrs['country_code'])) {
                if (isset($decoded['list'])) {
                    foreach ($decoded['list'] as $item) {
                        if ($item['country_code'] === $attrs['country_code']) {
                            $page = $item;
                            break;
                        }
                    }
                }
            }
            
            if ($page === null) {
                $page = $this->getDefaultPage();
            }
            
            if (!empty($attrs['path'])) {
                $value = $this->pathAccessor($attrs['path'], $page);
                if (is_array($value)) {
                    return \json_encode($value);
                } else {
                    return $value;
                }
            }
            
            return \json_encode($page);
            
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        
        return null;
    }
}
