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
    private $page = null;
    
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
     * @return array
     * @throws \Http\Client\Exception
     */
    private function getDefaultPage()
    {
        $response = $this->contentProviderService->execute();
        $decoded = \json_decode($response, true);
        
        if (is_array($decoded) && isset($decoded['list'])) {
            foreach ($decoded['list'] as $page) {
                if ($page['country_code'] === null) {
                    return $page;
                }
            }
        }
        
        throw new \UnexpectedValueException('Default page doesn\'t find out.');
    }
    
    /**
     * @param null $path
     *
     * @return mixed|null|string
     */
    private function pathAccessor($path = null)
    {
        if ($this->page === null){
            throw new \UnexpectedValueException('You can\'t use path access to null data.');
        }
        
        if ($path === null){
            return $this->page;
        }
        
        $path = preg_replace_callback('/(\.?[^.]+\.?)/', function ($matches) {
            return '[' . trim($matches[0], '.') . ']';
        }, $path);
        
        $accessor = PropertyAccess::createPropertyAccessor();
        
        if (preg_match('/^\[list\]\[([0-9]+)\](.*)$/', $path, $matches) != 0) {
            return $accessor->getValue($this->page, $matches[2]);
        }
        
        return $accessor->getValue($this->page, $path);
    }
    
    /**
     * Allowed params are:
     * ```
     * country_auto_resolve
     * name
     * country_code
     * ```
     *
     * @param array $attrs shortcode attributes
     * @return $this
     */
    public function retrieve($attrs = [])
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
            
            if (!empty($attrs['country_code'])) {
                if (isset($decoded['list'])) {
                    foreach ($decoded['list'] as $item) {
                        if ($item['country_code'] === $attrs['country_code']) {
                            $this->page = $item;
                            break;
                        }
                    }
                }
            }
            
            if ($this->page === null) {
                $this->page = $this->getDefaultPage();
            }
            
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        
        return $this;
    }
    
    /**
     * @param null $path
     *
     * @return false|mixed|null|string
     */
    public function path($path = null)
    {
        try {
            $value = $this->pathAccessor($path);
            if (is_array($value)) {
                return \json_encode($value);
            } else {
                return $value;
            }
        } catch (\Exception $e){
            $this->logger->error($e->getMessage());
        }
        
        return null;
    }
}
