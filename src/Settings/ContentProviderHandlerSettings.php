<?php

namespace BlackrockM\ContentProviderClient\Settings;

use function Blackrock\getenv;

/**
 * Class ContentProviderHandlerSettings
 * @package BlackrockM\ContentProviderClient\Handler\Factory
 */
class ContentProviderHandlerSettings
{
    /**
     * @var string
     */
    private $contentProviderUri;
    /**
     * @var string
     */
    private $contentProviderToken;

    /**
     * ContentProviderHandlerSettings constructor.
     * @param $contentProviderUri
     * @param $contentProviderToken
     */
    public function __construct($contentProviderUri, $contentProviderToken)
    {
        $this->contentProviderUri = $contentProviderUri;
        $this->contentProviderToken = $contentProviderToken;
    }

    /**
     * @return string
     */
    public function getContentProviderUri()
    {
        return $this->contentProviderUri;
    }

    /**
     * @return string
     */
    public function getContentProviderToken()
    {
        return $this->contentProviderToken;
    }
}
