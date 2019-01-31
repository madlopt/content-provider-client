<?php

namespace BlackrockM\ContentProviderClient;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ContentProviderShortcodeHandler
{
    /** @var ContentProviderListRetrieverService */
    private $contentProviderListRetrieverService;
    /** @var GeoIPService */
    private $geoIPService;
    /** @var string|null */
    private $defaultPage = null;

    /**
     * ContentProviderShortcodeHandler constructor.
     * @param ContentProviderListRetrieverService $contentProviderListRetrieverService
     * @param GeoIPService $geoIPService
     */
    public function __construct(
        ContentProviderListRetrieverService $contentProviderListRetrieverService,
        GeoIPService $geoIPService
    ) {
        $this->contentProviderListRetrieverService = $contentProviderListRetrieverService;
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
            $response = $this->contentProviderListRetrieverService->execute([]);
            $decoded = \json_decode($response, true);

            if (is_array($decoded) && isset($decoded['list'])) {
                foreach ($decoded['list'] as $page) {
                    if ($page['country_code'] === null) {
                        return $this->defaultPage = $page;
                    }
                }
            }
        }

        return $this->defaultPage;
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
     * Detect user country
     *
     * @return array
     */
    private function detectCountry()
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $this->geoIPService->detect($ip);
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
     * @return string
     */
    public function execute($attrs = [])
    {
        if ((!isset($attrs['country_auto_resolve']) || $attrs['country_auto_resolve'] === 'true') &&
            empty($attrs['country_code'])) {

            $geoIp = $this->detectCountry();
            $attrs['country_code'] = $geoIp['country_code'];
        }

        $requestAttrs = array_intersect_key($attrs, array_flip(['name', 'country_code']));

        $response = $this->contentProviderListRetrieverService->execute($requestAttrs);
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
    }
}
