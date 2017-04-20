<?php

namespace Freyo\Flysystem\QcloudCOSv3;

use Freyo\Flysystem\QcloudCOSv3\Client\Conf;
use Freyo\Flysystem\QcloudCOSv3\Client\Cosapi;
use Freyo\Flysystem\QcloudCOSv3\Exceptions\RuntimeException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Util;

/**
 * Class Adapter.
 */
class Adapter extends AbstractAdapter
{
    /**
     * @var
     */
    protected $bucket;

    /**
     * Adapter constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        Conf::setAppId($config['app_id']);
        Conf::setSecretId($config['secret_id']);
        Conf::setSecretKey($config['secret_key']);

        $this->bucket = $config['bucket'];

        $this->setPathPrefix($config['protocol'] . '://' . $config['domain'] . '/');

        Cosapi::setTimeout($config['timeout']);
    }

    /**
     * @return mixed
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param $path
     *
     * @return string
     */
    public function getUrl($path)
    {
        return $this->applyPathPrefix($path);
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     *
     * @return bool
     */
    public function write($path, $contents, Config $config)
    {
        $tmpfname = tempnam('/tmp', 'dir');
        chmod($tmpfname, 0777);
        file_put_contents($tmpfname, $contents);

        $response = $this->normalizeResponse(
            Cosapi::upload($this->getBucket(), $tmpfname, $path)
        );

        unlink($tmpfname);

        $this->setContentType($path, $contents);

        return $response;
    }

    /**
     * @param string   $path
     * @param resource $resource
     * @param Config   $config
     *
     * @return bool
     */
    public function writeStream($path, $resource, Config $config)
    {
        $uri = stream_get_meta_data($resource)['uri'];

        $response = $this->normalizeResponse(
            Cosapi::upload($this->getBucket(), $uri, $path)
        );

        $this->setContentType($path, stream_get_contents($resource));

        return $response;
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     *
     * @return bool
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * @param string   $path
     * @param resource $resource
     * @param Config   $config
     *
     * @return bool
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {
        return $this->normalizeResponse(
            Cosapi::moveFile($this->getBucket(), $path, $newpath)
        );
    }

    /**
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        return $this->normalizeResponse(
            Cosapi::copyFile($this->getBucket(), $path, $newpath)
        );
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        return $this->normalizeResponse(
            Cosapi::delFile($this->getBucket(), $path)
        );
    }

    /**
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        return $this->normalizeResponse(
            Cosapi::delFolder($this->getBucket(), $dirname)
        );
    }

    /**
     * @param string $dirname
     * @param Config $config
     *
     * @return bool
     */
    public function createDir($dirname, Config $config)
    {
        return $this->normalizeResponse(
            Cosapi::createFolder($this->getBucket(), $dirname)
        );
    }

    /**
     * @param string $path
     * @param string $visibility
     */
    public function setVisibility($path, $visibility)
    {
        $visibility = $visibility === AdapterInterface::VISIBILITY_PUBLIC ? 'eWPrivateRPublic' : 'eWRPrivate';

        return $this->normalizeResponse(
            Cosapi::update($this->getBucket(), $path, null, $visibility)
        );
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        return $this->getMetadata($path) !== false;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function read($path)
    {
        return ['contents' => file_get_contents($this->getUrl($path))];
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function readStream($path)
    {
        return ['stream' => fopen($this->getUrl($path), 'r')];
    }

    /**
     * @param string $directory
     * @param bool   $recursive
     *
     * @return bool
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->normalizeResponse(
            Cosapi::listFolder($this->getBucket(), $directory)
        );
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function getMetadata($path)
    {
        return $this->normalizeResponse(
            Cosapi::stat($this->getBucket(), $path)
        );
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function getSize($path)
    {
        $stat = $this->getMetadata($path);

        if ($stat) {
            return ['size' => $stat['filesize']];
        }

        return ['size' => 0];
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function getMimetype($path)
    {
        $stat = $this->getMetadata($path);

        if ($stat && !empty($stat['custom_headers']) && !empty($stat['custom_headers']['Content-Type'])) {
            return ['mimetype' => $stat['custom_headers']['Content-Type']];
        }

        return ['mimetype' => ''];
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function getTimestamp($path)
    {
        $stat = $this->getMetadata($path);

        if ($stat) {
            return ['timestamp' => $stat['ctime']];
        }

        return ['timestamp' => 0];
    }

    /**
     * @param string $path
     */
    public function getVisibility($path)
    {
        $stat = $this->getMetadata($path);

        $visibility = AdapterInterface::VISIBILITY_PRIVATE;

        if ($stat && $stat['authority'] === 'eWPrivateRPublic') {
            $visibility = AdapterInterface::VISIBILITY_PUBLIC;
        }

        return ['visibility' => $visibility];
    }

    /**
     * @param $path
     * @param $content
     *
     * @return bool
     */
    protected function setContentType($path, $content)
    {
        $custom_headers = [
            'Content-Type' => Util::guessMimeType($path, $content),
        ];

        return $this->normalizeResponse(
            Cosapi::update($this->getBucket(), $path, null, null, $custom_headers)
        );
    }

    /**
     * @param $response
     *
     * @return mixed
     * @throws RuntimeException
     */
    protected function normalizeResponse($response)
    {
        $response = is_array($response) ? $response : json_decode($response, true);

        if ($response && $response['code'] == 0) {
            return $response['data'];
        }

        // ErrSameFileUpload
        if ($response['code'] == -4018) {
            return true;
        }

        throw new RuntimeException($response['message'], $response['code']);
    }
}
