# Plugin for integration ContentProvider to WordPress

Plugin will try find value with passed parameters.

If result not found then will return `default` page.

### Example of usage

Return concrete value just using `path` parameter:

```php
ContentProviderShortcodeHandler::execute(['name' => 'NAME', 'country_code' => 'COUNTRY_CODE', 'path' => 'addresses.0.address']);
```

Or don't pass `country`. Plugin will try to found country based on user `IP`

```php
ContentProviderShortcodeHandler::execute(['name' => 'NAME']);
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
