# Sample Backend for PHP

This repository contains a sample backend code that demonstrates how to generate a Virgil JWT using the [PHP SDK](https://github.com/VirgilSecurity/virgil-sdk-php)

Do not use this authentication in production. Requests to /virgil-jwt endpoint must be allowed for authenticated users. Use your application authorization strategy.

## Installation

### Prerequisites

* PHP7.2 / PHP7.3
* **vscf_foundation_php**, **vscp_pythia_php**, **vsce_phe_php** extensions

### Install dependencies and extensions

```
$ composer install
```

Note that required Virgil extensions installs automatically as post install composer script  (see composer.json)
```
Сrypto extensions installation...
----------
Checking input... [OK]
Checking PHP version... [OK]
Checking OS... [OK]
Checking package version... [OK]
Checking PHP extensions directory... [OK]
Checking additional .ini files directory... [OK]
----------
SYSTEM CONFIGURATION:
Crypto version: 
OS (short): Linux
PHP version (short): 7.2
PHP version (full):
PHP 7.2.26 (cli) (built: Dec 22 2019 06:01:52) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Xdebug v2.6.1, Copyright (c) 2002-2018, by Derick Rethans
Extensions directory: /usr/lib/php7/modules
Additional .ini files directory: /etc/php7/conf.d /etc/php7/conf.d
----------
Copying vendor/virgil/crypto-wrapper/_extensions/bin/lin/php7.2/vsce_phe_php7.2_v0.15.2.so to the /usr/lib/php7/modules/... [OK]
Copying vendor/virgil/crypto-wrapper/_extensions/bin/lin/php7.2/vscf_foundation_php7.2_v0.15.2.so to the /usr/lib/php7/modules/... [OK]
Copying vendor/virgil/crypto-wrapper/_extensions/bin/lin/php7.2/vscp_pythia_php7.2_v0.15.2.so to the /usr/lib/php7/modules/... [OK]
Copying vendor/virgil/crypto-wrapper/_extensions/bin/lin/php7.2/virgil_crypto.ini file to the /etc/php7/conf.d/virgil_crypto.ini... [OK]
Copying vendor/virgil/crypto-wrapper/_extensions/bin/lin/php7.2/virgil_crypto.ini file to the /etc/php7/conf.d/virgil_crypto.ini... [OK]
----------
STATUS: Restart your webserver (or php-service if available)

```
Ensure that **vscf_foundation_php**, **vscp_pythia_php**, **vsce_phe_php** extensions is present in command output after composer install
```bash
php -m 
```

### Get Virgil Credentials

If you don't have an account yet, [sign up for one](https://dashboard.virgilsecurity.com/signup) using your e-mail.

To generate a JWT the following values are required:

| Variable Name                     | Description                    |
|-----------------------------------|--------------------------------|
| APP_KEY                  | Private key of your API key that is used to sign the JWTs. |
| APP_KEY_ID               | ID of your API key. A unique string value that identifies your account in the Virgil Cloud. |
| APP_ID                   | ID of your Virgil Application. |

### Add Virgil Credentials to the .env

- create a `.env` file from the `.env.example` and fill it with your account credentials

## Configure and Run Server

Make sure that `./app/public/` is a public-accessible directory with a `index.php` file. Also, you need to make a front-controller and rewrite all requests to the `index.php` file.

More info on how to configure and run a Apache/nginx/hhvm server can be found [here](https://www.slimframework.com/docs/v3/start/web-servers.html).

## Specification

### /authenticate endpoint
This endpoint is an example of users authentication. It takes user `identity` and responds with unique token.

```http
POST https://<server_name>/authenticate HTTP/1.1
Content-type: application/json;

{
    "identity": "string"
}

Response:

{
    "authToken": "string"
}
```

<p><img src="https://raw.githubusercontent.com/VirgilSecurity/sample-backend-php/master/_help/s-4.png" width="60%"></p>

### /virgil-jwt endpoint
This endpoint checks whether a user is authorized by an authorization header. It takes user's `authToken`, finds related user identity and generates a `virgilToken` (which is [JSON Web Token](https://jwt.io/)) with this `identity` in a payload. Use this token to make authorized api calls to Virgil Cloud.

```http
GET https://<server_name>/virgil-jwt HTTP/1.1
Content-type: application/json;
Authorization: Bearer <authToken>

Response:

{
    "virgilToken": "string"
}
```

<p><img src="https://raw.githubusercontent.com/VirgilSecurity/sample-backend-php/master/_help/s-5.png" width="60%"></p>

## Virgil JWT Generation
To generate JWT, you need to use the `JwtGenerator` class from the SDK.

```php
$privateKeyStr = $_ENV['APP_KEY'];
$apiKeyData = base64_decode($privateKeyStr);

$crypto = new VirgilCrypto();
$privateKey = $crypto->importPrivateKey($apiKeyData);

$appId = $_ENV['APP_ID'];
$apiKeyId = $_ENV['APP_KEY_ID'];
$ttl = 3600;

$jwtGenerator = new SDKJwtGenerator($privateKey->getPrivateKey(), $apiKeyId, $crypto, $appId, $ttl);

$token = $jwtGenerator->generateToken($identity);

$jwt = $token->__toString();

```
Then you need to provide an HTTP endpoint which will return the JWT with the user's identity as a JSON.

For more details take a look at the [JWTGenerator.php](app/core/JWTGenerator.php) file.

## License

This library is released under the [3-clause BSD License](LICENSE.md).

## Support
Our developer support team is here to help you. Find out more information on our [Help Center](https://help.virgilsecurity.com/).

You can find us on [Twitter](https://twitter.com/VirgilSecurity) or send us email support@VirgilSecurity.com.

Also, get extra help from our support team on [Slack](https://virgilsecurity.com/join-community).
