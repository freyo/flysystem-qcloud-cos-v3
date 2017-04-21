# flysystem-qcloud-cos-v3

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/freyo/flysystem-qcloud-cos-v3.svg?style=flat-square)](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v3)
[![Total Downloads](https://img.shields.io/packagist/dt/freyo/flysystem-qcloud-cos-v3.svg?style=flat-square)](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v3)

This is a Flysystem adapter for the qcloud-cos-sdk-php v3.

腾讯云COS对象存储 V3

## Attention

if you are a new registered user(after October 2016), [v4](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v4) should be used.

2016年10月以后新注册的用户默认使用[V4版本](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v4)

if you have used COS before October 2016, [v3](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v3) can continue to use.

2016年10月之前使用COS的用户可以继续使用[V3版本](https://packagist.org/packages/freyo/flysystem-qcloud-cos-v3)

## Installation

  ```shell
  composer require freyo/flysystem-qcloud-cos-v3
  ```

## Bootstrap

  ```php
  <?php
  use Freyo\Flysystem\QcloudCOSv3\Adapter;
  use League\Flysystem\Filesystem;

  include __DIR__ . '/vendor/autoload.php';

  $config = [
      'protocol' => 'http',
      'domain' => 'your-domain',
      'app_id' => 'your-app-id',
      'secret_id' => 'your-secret-id',
      'secret_key' => 'your-secret-key',
      'timeout' => 60,
      'bucket' => 'your-bucket-name',
  ];

  $adapter = new Adapter($config);
  $filesystem = new Filesystem($adapter);
  ```

## Use in Laravel

1. Register `config/app.php`:

  ```php
  Freyo\Flysystem\QcloudCOSv3\ServiceProvider::class,
  ```

2. Configure `config/filesystems.php`:

  ```php
  'disks'=>[
      'cosv3' => [
          'driver' => 'cosv3',
          'protocol' => env('COSV3_PROTOCOL', 'http'),
          'domain' => env('COSV3_DOMAIN'),
          'app_id' => env('COSV3_APP_ID'),
          'secret_id' => env('COSV3_SECRET_ID'),
          'secret_key' => env('COSV3_SECRET_KEY'),
          'timeout' => env('COSV3_TIMEOUT', 60),
          'bucket' => env('COSV3_BUCKET'),
      ],
  ],
  ```
