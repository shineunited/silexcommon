Shine United - Silex Common
===========================


Installation
------------

The recommended way to install is through
[Composer](http://getcomposer.org).

#### Install Composer
```bash
$ curl -sS https://getcomposer.org/installer | php
```

#### Require Silex Common
```bash
$ composer require shineunited/silexcommon
```


Usage
-----

```php
<?php

include('../vendor/autoload.php');

use ShineUnited\Silex\Common\Application;

$app = new Application();

// configure application

$app->run();
```


Service Providers
-----------------
- Silex\\Provider\\DoctrineServiceProvider
- Silex\\Provider\\MonologServiceProvider
- Silex\\Provider\\SwiftmailerServiceProvider
- Silex\\Provider\\TranslationServiceProvider
- Silex\\Provider\\TwigServiceProvider
- Silex\\Provider\\UrlGeneratorServiceProvider
- Silex\\Provider\\ValidatorServiceProvider

Application Traits
------------------
- Silex\\Application\\MonologTrait
- Silex\\Application\\SwiftmailerTrait
- Silex\\Application\\TranslationTrait
- Silex\\Application\\TwigTrait
- Silex\\Application\\UrlGeneratorTrait
