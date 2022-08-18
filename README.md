# Facebook Graph PHP SDK

This repository contains the open source PHP SDK that allows you to access the Facebook Platform from your PHP app. It is modified to allow compatibility, view the previous SDK version [here](https://github.com/facebookarchive/php-graph-sdk)

<p align="center">
    <a href="https://github.com/joelbutcher/facebook-php-graph-sdk/actions">
        <img src="https://github.com/joelbutcher/facebook-php-graph-sdk/workflows/tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://packagist.org/packages/heniroger/facebook-graph-sdk">
        <img src="https://img.shields.io/packagist/dt/heniroger/facebook-graph-sdk" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/heniroger/facebook-graph-sdk">
        <img src="https://img.shields.io/packagist/v/heniroger/facebook-graph-sdk" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/heniroger/facebook-graph-sdk">
        <img src="https://img.shields.io/packagist/l/heniroger/facebook-graph-sdk" alt="License">
    </a>
</p>


## Installation

The Facebook PHP SDK can be installed with [Composer](https://getcomposer.org/). Run this command:

```sh
composer require heniroger/facebook-graph-sdk
```

## Usage

> **Note:** This version of the Facebook SDK for PHP requires PHP 8.0 or greater.

Simple GET example of a user's profile.

```php
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed

$fb = new \Facebook\Facebook([
  'app_id' => '{app-id}',
  'app_secret' => '{app-secret}',
  'default_graph_version' => 'v2.10',
  //'default_access_token' => '{access-token}', // optional
]);

// Use one of the helper classes to get a Facebook\Authentication\AccessToken instance.
//   $helper = $fb->getRedirectLoginHelper();
//   $helper = $fb->getJavaScriptHelper();
//   $helper = $fb->getCanvasHelper();
//   $helper = $fb->getPageTabHelper();

try {
  // Get the \Facebook\GraphNodes\GraphUser object for the current user.
  // If you provided a 'default_access_token', the '{access-token}' is optional.
  $response = $fb->get('/me', '{access-token}');
} catch(\Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(\Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$me = $response->getGraphUser();
echo 'Logged in as ' . $me->getName();
```

Complete documentation, installation instructions, and examples are available [here](docs/).

## Tests

1. [Composer](https://getcomposer.org/) is a prerequisite for running the tests. Install composer globally, then run `composer install` to install required files.
2. Create a test app on [Facebook Developers](https://developers.facebook.com), then create `tests/FacebookTestCredentials.php` from `tests/FacebookTestCredentials.php.dist` and edit it to add your credentials.
3. The tests can be executed by running this command from the root directory:

```bash
$ ./vendor/bin/phpunit
```

By default the tests will send live HTTP requests to the Graph API. If you are without an internet connection you can skip these tests by excluding the `integration` group.

```bash
$ ./vendor/bin/phpunit --exclude-group integration
```

## Contributing

For us to accept contributions you will have to first have signed the [Contributor License Agreement](https://developers.facebook.com/opensource/cla). Please see [CONTRIBUTING](https://github.com/heniroger/facebook-graph-sdk-php-8/blob/master/CONTRIBUTING.md) for details.

## License

Please see the [license file](https://github.com/heniroger/facebook-graph-sdk-php-8/blob/master/LICENSE) for more information.

## Security Vulnerabilities

If you have found a security issue, please contact the maintainers directly at [joel@joelbutcher.co.uk](mailto:joel@joelbutcher.co.uk).
