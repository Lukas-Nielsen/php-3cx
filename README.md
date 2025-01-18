# php-3cx

package to wrap the api of [3cx®](https://www.3cx.com/)

- first steps: https://www.3cx.com/docs/configuration-rest-api/
- openapi documentation: https://downloads-global.3cx.com/downloads/misc/restapi/3cxconfigurationapi.yaml

## getting started

```sh
composer require ln/threecx
```

## usage

### conf

you need the FQDN of the instanz, username and password (with permissions for the action)

```php
// conf
$config = new Config();
$config->fqdn = "company.my3cx.com";
$config->port = 443; // can be omited
$config->user = "admin";
$config->password = "super-secret";
$config->debug = false; // can be omited, debug via guzzlehttp/guzzle
$config->token = new Token();
// if you already have a token
$config->token->accessToken = "access token";
$config->token->tokenType = "Bearer";
$config->token->refreshToken = "refresh token";
$config->token->expires = 1721512;

$client = new Client($config);
```

### functions

#### get

```php
$client->get(string <uri>, array <query>)
```

#### delete

```php
$config->delete(string <uri>, array <query>)
```

#### post, put, patch

```php
$config-><post|put|patch>(string <uri>, array <payload>, array <query>)
```
