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
     * @param array $attrs shortcode attributes
     * @return string|mixed
     */
    public function execute($attrs = [])
    {
        if (empty($attrs['country_code'])) {
            if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            $geoIp = $this->geoIPService->detect($ip);
            $attrs['country_code'] = $geoIp['country_code'];
        }

        $requestAttrs = array_intersect_key($attrs, array_flip(['name', 'country_code']));

        $response = $this->contentProviderListRetrieverService->execute($requestAttrs);

        if (!empty($attrs['path'])) {

            $path = preg_replace_callback('/(\.?[^.]+\.?)/', function ($matches) {
                return '[' . trim($matches[0], '.') . ']';
            }, $attrs['path']);

            $accessor = PropertyAccess::createPropertyAccessor();
            $decoded = json_decode($response, true);

            // If country code is specified we need check that item has these country. If not then return default.
            if (preg_match('/^\[list\]\[([0-9]+)\](.*)$/', $path, $matches) != 0 &&
                !empty($attrs['country_code'])) {

                $list = $accessor->getValue($decoded, '[list]');

                if ($list !== null) {
                    foreach ($list as $item) {
                        if ($item['country_code'] === $attrs['country_code']) {
                            $value = $accessor->getValue($item, $matches[2]);
                            if ($value !== null) {
                                return $value;
                            }
                        }
                    }
                }

                if ($this->defaultPage === null) {
                    $this->defaultPage = $this->contentProviderListRetrieverService->execute(['name' => $attrs['name']]);
                }

                if ($this->defaultPage !== null) {
                    $decoded = json_decode($this->defaultPage, true);
                    foreach ($decoded['list'] as $item) {
                        if ($item['country_code'] === null) {
                            return $accessor->getValue($item, $matches[2]);
                        }
                    }
                }

                return null;
            }

            return $accessor->getValue($decoded, $path);
        }

        return $response;
    }
}
