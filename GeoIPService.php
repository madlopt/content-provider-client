<?php

namespace BlackrockM\ContentProviderClient;

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
     * @param string $ip
     * @return array
     * @throws \RuntimeException
     */
    public function detect($ip)
    {
        $url = $this->url . $ip . '?user=' . $this->user . '&pass=' . $this->pass;
        $content = file_get_contents($url);
        if ($content === false) {
            throw new \RuntimeException('Could not fetch data from server');
        }

        return json_decode($content, true);
    }
}
