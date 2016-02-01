# laravel4-storage

A filesystem abstraction library for Laravel 4

[![Author](http://img.shields.io/badge/author-@superbalist-blue.svg?style=flat-square)](https://twitter.com/superbalist)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)


This package brings a filesystem abstraction layer to Laravel 4.  This is an organic feature of Laravel 5 which uses the
flysystem package under the hood.  Please note that this is not a like-for-like version of the Laravel 5 package and that
there may be suttle differences in configuration and method names.  We're looking to make this package identical in a
future major version to make the Laravel 4 -> 5 upgrade less painful.

## Supported Adapters

* Local
* Rackspace (Cloud Files)
* Amazon Web Services (S3)
* Google Cloud

## Installation

```bash
composer require superbalist/laravel4-storage
```

Register the service provider in app.php
```php
'providers' => array(
    'Superbalist\Storage\StorageServiceProvider',
)
```

Register the facade in app.php
```php
'aliases' => array(
    'Storage' => 'Superbalist\Storage\StorageFacade',
)
```

Create a storage.php config file.
```php
<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Connection
    |--------------------------------------------------------------------------
    |
    | The default file system connection to use.
    |
    | A non-default connection can also be specified by using \Storage::connection('name')->put(...)
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Connections
    |--------------------------------------------------------------------------
    |
    | The various connection configs.
    |
    */

    'connections' => array(
        'local' => array(
            'adapter' => 'local',
            'root_path' => storage_path(),
            'public_url_base' => '[[http://a.public.url.to.your.service/storage]]',
        ),

        'rackspace' => array(
            'adapter' => 'rackspace',
            'store' => 'cloudFiles',
            'region' => 'LON',
            'container' => '[[insert your cdn container name]]',
        ),

        'gcloud' => array(
            'adapter' => 'gcloud',
            'bucket' => '[[insert your bucket name]]',
        ),
    ),

);
```

Add settings to the services.php config file, depending on which storage adapter(s) you're using.
```php
<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | AWS
    |--------------------------------------------------------------------------
    |
    */

    'aws' => array(
        'access_key' => '[[your aws key]]',
        'secret_key' => '[[your aws secret]]',
        'region' => '[[your aws region]]',
    ),

    /*
    |--------------------------------------------------------------------------
    | Google Cloud
    |--------------------------------------------------------------------------
    |
    */

    'google_cloud' => array(
        'service_account' => '[[your service account]',
        'key_file' => '[[path to the p12 key file]]',
        'secret' => '[[your secret]]',
        'developer_key' => '[[your developer key]]',
    ),

    /*
    |--------------------------------------------------------------------------
    | Rackspace
    |--------------------------------------------------------------------------
    |
    */

    'rackspace' => array(
        'username' => '[[your username]]',
        'tenant_name' => '[[your tenant name]]',
        'api_key' => '[[your api key]]',
        'api_endpoint' => \OpenCloud\Rackspace::UK_IDENTITY_ENDPOINT,
    ),

);
```

## Usage

```php
// TODO: add usage examples
```