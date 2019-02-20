<?php

namespace BlackrockM\ContentProviderClient\Utils;

use Http\Client\Common\HttpMethodsClient;

/**
 * Class GeoIPService
 *
 * Call api to detect a user GeoIP info
 */
class GeoIPService
{
    const LOCALHOST = '127.0.0.1';

    /** @var HttpMethodsClient */
    private $client;

    /**
     * GeoIPService constructor.
     * @param HttpMethodsClient $client
     */
    public function __construct(HttpMethodsClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     * @throws \RuntimeException
     * @throws \Http\Client\Exception
     */
    public function getGeoData()
    {
        if ($this->detectIp() === self::LOCALHOST) {
            return $this->getLocalhostGeoData();
        }

        $response = $this->client->get('/country/' . $this->detectIp());
        $content = $response->getBody()->getContents();

        return json_decode($content, true);
    }

    /**
     * @return array
     */
    private function getLocalhostGeoData()
    {
        return [
            'country_code' => null,
        ];
    }

    /**
     * Detect user country
     *
     * @return string
     */
    private function detectIp()
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
