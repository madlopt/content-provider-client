<?php

namespace BlackrockM\ContentProviderClient\Provider;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContentProviderRequestObject
 * @package BlackrockM\ContentProviderClient\Provider
 */
class RequestObject
{
    /**
     * @var string|null
     */
    private $name;
    
    /**
     * @var string|null
     */
    private $countryCode;
    
    /**
     * @var integer|null
     */
    private $page;
    
    /**
     * @var integer|null
     */
    private $limit;
    
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'country_code' => $this->countryCode,
            'page' => $this->page,
            'limit' => $this->limit,
        ];
    }
    
    /**
     * @param array $array
     */
    public static function createFromArray(array $array)
    {
        $resolver = new OptionsResolver();
    
        $resolver->setDefined(['page', 'limit', 'name', 'country_code']);
        $resolver->setAllowedTypes('page', ['null', 'integer']);
        $resolver->setAllowedTypes('limit', ['null', 'integer']);
        $resolver->setAllowedTypes('name', ['null', 'string']);
        $resolver->setAllowedTypes('country_code', ['null', 'string']);
    
        $array = $resolver->resolve($array);
        
        $obj = new self;
        $obj->name = $array['name'];
        $obj->countryCode = $array['country_code'];
        $obj->page = $array['page'];
        $obj->limit = $array['limit'];
        
        return $obj;
    }
}
