<?php

namespace BlackrockM\ContentProviderClient\HttpClient\Formatter;

use Http\Message\Formatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpClientFormatter
 * @package Blackrock\Proftit\HttpClient\Formatter
 */
class HttpClientFormatter implements Formatter
{
    public function formatRequest(RequestInterface $request)
    {
        return sprintf(
            '%s %s, data: %s',
            $request->getMethod(),
            $request->getUri()->__toString(),
            $request->getBody()
        );
    }
    
    /**
     * Formats a response.
     *
     * @param ResponseInterface $response
     *
     * @return string
     */
    public function formatResponse(ResponseInterface $response)
    {
        return sprintf(
            '%s %s',
            $response->getStatusCode(),
            $response->getBody()
        );
    }
}

