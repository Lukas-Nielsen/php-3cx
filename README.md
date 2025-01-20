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
$client = new Client(new Host(string <fqdn>, int <port>, bool <debug, optional>));

$client->setUser(new User(string <username>, string <password>, string <mfa, optional>));
// or
$client->setRest(new Rest(string <clientId>, string <clientSecret>));
// or
$client->setToken(new Token(string <tokenType>, int <expires>, string <accessToken>, string <refreshToken>));
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
