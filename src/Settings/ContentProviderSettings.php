<?php

namespace BlackrockM\ContentProviderClient\Settings;

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
    public function __construct($uri, $token)
    {
        $this->uri = $uri;
        $this->token = $token;
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
