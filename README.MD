# Client for integration ContentProviderService

Plugin will try find value with passed parameters.

If result not found then will return `default` page.

### Example of usage

Create ContentProviderHandler:

```php
use BlackrockM\ContentProviderClient\Handler\Factory\ContentProviderHandlerFactory;

$handler = (new ContentProviderHandlerFactory())->create();
```

Return concrete value just using `path` parameter:

```php
$page = $handler->retrieve(['name' => 'NAME', 'country_code' => 'COUNTRY_CODE']);

echo $page->path('addresses.0.address'); //return first address
echo $page->path('company');
echo $page->path('phones.1.phone'); //return second phone
```

Or don't pass `country`. Plugin will try to found country based on user `IP`

```php
$page = $handler->retrieve(['name' => 'NAME']);
echo $page->path(); //return full json response, see below
```

### Correct response
```json
{
  "id": 0,
  "addresses": [
    {
      "address": "string"
    }
  ],
  "phones": [
    {
      "phone": "string",
      "schedule": "string"
    }
  ],
  "emails": [
    {
      "email": "string"
    }
  ],
  "name": "string",
  "country_code": "string"
}
```
