# flysystem-qcloud-cos-v3

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/freyo/flysystem-qcloud-cos-v3/master.svg?style=flat-square)](https://travis-ci.org/freyo/flysystem-qcloud-cos-v3)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/freyo/flysystem-qcloud-cos-v3.svg?style=flat-square)](https://scrutinizer-ci.com/g/freyo/flysystem-qcloud-cos-v3)
[![Quality Score](https://img.shields.io/scrutinizer/g/freyo/flysystem-qcloud-cos-v3.svg?style=flat-square)](https://scrutinizer-ci.com/g/freyo/flysystem-qcloud-cos-v3)
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
      'debug' => false,
  ];

  $adapter = new Adapter($config);
  $filesystem = new Filesystem($adapter);
  ```
  
### API

```php
bool $flysystem->write('file.md', 'contents');

bool $flysystem->writeStream('file.md', fopen('path/to/your/local/file.jpg', 'r'));

bool $flysystem->update('file.md', 'new contents');

bool $flysystem->updateStram('file.md', fopen('path/to/your/local/file.jpg', 'r'));

bool $flysystem->rename('foo.md', 'bar.md');

bool $flysystem->copy('foo.md', 'foo2.md');

bool $flysystem->delete('file.md');

bool $flysystem->has('file.md');

string|false $flysystem->read('file.md');

array $flysystem->listContents();

array $flysystem->getMetadata('file.md');

int $flysystem->getSize('file.md');

string $flysystem->getUrl('file.md'); 

string $flysystem->getMimetype('file.md');

int $flysystem->getTimestamp('file.md');

string $flysystem->getVisibility('file.md');

bool $flysystem->setVisibility('file.md', 'public'); //or 'private'
```

[Full API documentation.](http://flysystem.thephpleague.com/api/)

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
          'debug' => env('COSV3_DEBUG', false),
      ],
  ],
  ```
### Usage

```php
$disk = Storage::disk('cosv3');

// create a file
$disk->put('avatars/1', $fileContents);

// check if a file exists
$exists = $disk->has('file.jpg');

// get timestamp
$time = $disk->lastModified('file1.jpg');

// copy a file
$disk->copy('old/file1.jpg', 'new/file1.jpg');

// move a file
$disk->move('old/file1.jpg', 'new/file1.jpg');

// get file contents
$contents = $disk->read('folder/my_file.txt');

// get url
$url = $disk->url('new/file1.jpg');

// create a file from remote(plugin support)
$disk->putRemoteFile('avatars/1', 'http://example.org/avatar.jpg');
$disk->putRemoteFileAs('avatars/1', 'http://example.org/avatar.jpg', 'file1.jpg');
```

[Full API documentation.](https://laravel.com/api/5.4/Illuminate/Contracts/Filesystem/Cloud.html)
