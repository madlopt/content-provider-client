<?php

use BlackrockM\ContentProviderClient\Handler\Factory\ContentProviderHandlerFactory;

$handler = (new ContentProviderHandlerFactory())->create();

add_shortcode('content_provider', function($atts) use ($handler)
{
    $page = $handler->retrieve($atts);
    
    if (!empty($atts['path'])){
        return $page->path($atts['path']);
    }
    
    return $page->path();
});
