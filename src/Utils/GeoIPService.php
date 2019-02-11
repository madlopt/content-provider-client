<?php

namespace BlackrockM\ContentProviderClient\Utils;

/**
 * Class GeoIPService
 *
 * Call api to detect a user GeoIP info
 */
class GeoIPService
{
    /** @var string */
    private $url;
    /** @var string */
    private $user;
    /** @var string */
    private $pass;

    /**
     * GeoIPService constructor.
     * @param string $url
     * @param string $user
     * @param string $pass
     */
    public function __construct($url, $user, $pass)
    {
        $this->url = $url;
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getGeoData()
    {
        $url = $this->url . $this->detectIp() . '?user=' . $this->user . '&pass=' . $this->pass;
        $content = file_get_contents($url);
        if ($content === false) {
            throw new \RuntimeException('Could not fetch data from server');
        }

        return json_decode($content, true);
    }
    
    /**
     * Detect user country
     *
     * @return array
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
