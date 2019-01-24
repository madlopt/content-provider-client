<?php

namespace BlackrockM\ContentProviderClient;

use Symfony\Component\OptionsResolver\OptionsResolver;
use GuzzleHttp\Client;

class ContentProviderListRetrieverService
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
     * Validate params array
     *
     * @param array $params
     * @return array
     */
    private function resolve(array $params)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefined(['page', 'limit', 'name', 'country_code']);
        $resolver->setAllowedTypes('page', ['null', 'integer']);
        $resolver->setAllowedTypes('limit', ['null', 'integer']);
        $resolver->setAllowedTypes('name', ['null', 'string']);
        $resolver->setAllowedTypes('country_code', ['null', 'string']);

        return $resolver->resolve($params);
    }

    /**
     * Execute query and return response
     *
     * @param array $params API request params
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(array $params)
    {
        $params = $this->resolve($params);
        $params = array_filter($params, function ($value) {
            return $value !== null;
        });

        $client = new Client();
        $response = $client->request('GET', $this->url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'query' => $params
        ]);

        return $response->getBody()->getContents();
    }
}
