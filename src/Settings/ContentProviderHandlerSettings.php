<?php

namespace BlackrockM\ContentProviderClient\Settings;

use function Blackrock\getenv;

/**
 * Class ContentProviderHandlerSettings
 * @package BlackrockM\ContentProviderClient\Handler\Factory
 */
class ContentProviderSettings
{
    /**
     * @var string
     */
    private $uri;
    /**
     * @var string
     */
    private $token;

    /**
     * ContentProviderSettings constructor.
     * @param bool $uri
     * @param bool $token
     */
    public function __construct($uri = null, $token = null)
    {
        $this->uri = $uri === null ? getenv('CONTENT_PROVIDER_URI') : $uri;
        $this->token = $token === null ? getenv('CONTENT_PROVIDER_TOKEN') : $token;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
