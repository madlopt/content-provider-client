<?php

namespace BlackrockM\ContentProviderClient\Provider;

use Symfony\Component\OptionsResolver\OptionsResolver;
use GuzzleHttp\Client;

/**
 * Class ContentProviderService
 * @package BlackrockM\ContentProviderClient\Provider
 */
class ContentProviderService
{
    /** @var string */
    private $token;
    
    /** @var string */
    private $url;

    /**
     * @param string $url Url to API domain
     * @param string $token OAuth2 token
     */
    public function __construct($url, $token)
    {
        $this->url = $url;
        $this->token = $token;
    }

    /**
     * Execute query and return response
     *
     * @param array $params API request params
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(RequestObject $requestObject = null)
    {
        $client = new Client();
        $response = $client->request('GET', $this->url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'query' => $requestObject ? array_filter($requestObject->toArray(), 'strlen') : []
        ]);

        return $response->getBody()->getContents();
    }
}
