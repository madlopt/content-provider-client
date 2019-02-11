<?php

namespace BlackrockM\ContentProviderClient\Exception;

/**
 * Class Exception
 * @package BlackrockM\ContentProviderClient\Exception
 */
class Exception extends \Exception
{
    /**
     * @var string
     */
    private $info;
    
    /**
     * Exception constructor.
     *
     * @param string     $info
     * @param \Exception $previous
     */
    public function __construct(string $info, \Exception $previous)
    {
        $this->info = $info;
        parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
    }
    
    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }
}