<?php

namespace Freyo\Flysystem\QcloudCOSv3\Client;

class Conf
{
    const PKG_VERSION = 'v3.3';

    const API_IMAGE_END_POINT = 'http://web.image.myqcloud.com/photos/v1/';
    const API_VIDEO_END_POINT = 'http://web.video.myqcloud.com/videos/v1/';
    const API_COSAPI_END_POINT = 'http://web.file.myqcloud.com/files/v1/';

    //请到http://console.qcloud.com/cos去获取你的appid、sid、skey
    private static $APPID;
    private static $SECRET_ID;
    private static $SECRET_KEY;

    public static function setAppId($appId)
    {
        self::$APPID = $appId;
    }

    public static function setSecretId($secretId)
    {
        self::$SECRET_ID = $secretId;
    }

    public static function setSecretKey($secretKey)
    {
        self::$SECRET_KEY = $secretKey;
    }

    public static function getAppId()
    {
        return self::$APPID;
    }

    public static function getSecretId()
    {
        return self::$SECRET_ID;
    }

    public static function getSecretKey()
    {
        return self::$SECRET_KEY;
    }

    public static function getUA()
    {
        return 'cos-php-sdk-'.self::PKG_VERSION;
    }
}

//end of script
