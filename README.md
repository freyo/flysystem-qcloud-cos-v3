# laravel-cos-v3-adapter

Tencent Qcloud COS V3 Adapter for Laravel

腾讯云COS对象存储V3 for Laravel 5

## 安装

  ```shell
  composer require "freyo/laravel-cos-v3-adapter:dev-master"
  ```

## 配置

1. 在 `config/app.php` 中注册 `ServiceProvider`:

  ```php
  Freyo\LaravelQcloudCosV3\ServiceProvider::class,
  ```

2. 在 `config/filesystems.php` 中配置:

  ```php
  'disks'=>[
      'cosv3' => [
          'driver' => 'cosv3',
          'protocol' => env('COSV3_PROTOCOL', 'http'),
          'domain' => env('COSV3_DOMAIN'),
          'app_id' => env('COSV3_APPID'),
          'secret_id' => env('COSV3_SECRET_ID'),
          'secret_key' => env('COSV3_SECRET_KEY'),
          'timeout' => env('COSV3_PROTOCOL', 60),
          'bucket' => env('COSV3_BUCKET'),
      ],
  ],
  ```
