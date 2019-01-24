# Plugin for integration ContentProvider to WordPress

### Example of usage
```php
// Return concrete value just using `path` parameter:
ContentProviderShortcodeHandler::execute(['name' => 'NAME', 'country_code' => 'COUNTRY_CODE', 'path' => 'list.0.addresses.0.address']);

// Or get all response from server
ContentProviderShortcodeHandler::execute(['name' => 'NAME', 'country_code' => 'COUNTRY_CODE']);
```

### Correct response
```json
{
  "list": [
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
  ],
  "pagination": {
    "totalItems": 15,
    "itemsPerPage": 5,
    "currentPage": 1
  }
}
```

### Incorrect response structure
```json
{
  "errors": [
    {
      "NAME_OF_ERROR": "ERROR"
    }
  ]
}
```
