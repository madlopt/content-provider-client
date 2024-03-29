<?php

namespace BlackrockM\ContentProviderClient\Handler;

use BlackrockM\ContentProviderClient\Provider\RequestObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use BlackrockM\ContentProviderClient\Provider\ContentProviderService;
use BlackrockM\GeoIp\Client\Provider\GeoIpProvider;
use function Blackrock\json_decode;
use function Blackrock\json_encode;

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
     * @var GeoIpProvider
     */
    private $geoIPProvider;
    
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
     * @param GeoIpProvider           $geoIPProvider
     */
    public function __construct(
        LoggerInterface $logger,
        ContentProviderService $contentProviderService,
        GeoIpProvider $geoIPProvider
    ) {
        $this->logger = $logger;
        $this->contentProviderService = $contentProviderService;
        $this->geoIPProvider = $geoIPProvider;
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
        $decoded = json_decode($response, true);
        
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
     * @param array $attrs
     *
     * Allowed params are:
     * ```
     * country_auto_resolve
     * name
     * country_code
     *
     * @return $this
     * @throws \Http\Client\Exception
     */
    public function retrieve($attrs = [])
    {
        try {
            
            $this->page = null;
            
            if ((!isset($attrs['country_auto_resolve']) || $attrs['country_auto_resolve'] === 'true') &&
                empty($attrs['country_code'])) {
                
                $geoData = $this->geoIPProvider->retrieveArray();
                $attrs['country_code'] = $geoData['country_code'];
            }
            
            $requestAttrs = array_intersect_key($attrs, array_flip(['name', 'country_code']));
            
            $response = $this->contentProviderService->execute(RequestObject::createFromArray($requestAttrs));
            $decoded = json_decode($response, true);
            
            if (!empty($attrs['country_code']) && isset($decoded['list'])) {
                foreach ($decoded['list'] as $item) {
                    if ($item['country_code'] === $attrs['country_code']) {
                        $this->page = $item;
                        break;
                    }
                }
            }
            
            if (!$this->page && isset($decoded['list'][0])) {
                $this->page = $decoded['list'][0];
            }
            
            if (!$this->page) {
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
            return is_array($value) ? json_encode($value) : $value;
        } catch (\Exception $e){
            $this->logger->error($e->getMessage());
        }
        
        return null;
    }
}
