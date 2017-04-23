<?php

namespace Freyo\Flysystem\QcloudCOSv3\Tests;

use League\Flysystem\Config;
use PHPUnit\Framework\TestCase;
use Freyo\Flysystem\QcloudCOSv3\Adapter;

class AdapterTest extends TestCase
{
    public function Provider()
    {
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

        return [
            [$adapte, $config],
        ];
    }

    /**
     * @dataProvider Provider
     */
    public function testWrite($adapter)
    {
        $this->assertArrayHasKey('access_url', $adapter->write('foo/foo.md', 'content', new Config()));
    }

    /**
     * @dataProvider Provider
     */
    public function testWriteStream($adapter)
    {
        $this->assertArrayHasKey('access_url', $adapter->writeStream('foo/bar.md', tmpfile(), new Config()));
    }

    /**
     * @dataProvider Provider
     */
    public function testUpdate($adapter)
    {
        $this->assertArrayHasKey('access_url', $adapter->update('foo/bar.md', 'newcontent', new Config()));
    }

    /**
     * @dataProvider Provider
     */
    public function testUpdateStream($adapter)
    {
        $this->assertArrayHasKey('access_url', $adapter->updateStream('foo/foo.md', tmpfile(), new Config()));
    }

    /**
     * @dataProvider Provider
     */
    public function testRename($adapter)
    {
        $this->assertTrue($adapter->rename('foo/foo.md', 'foo/rename.md'));
    }

    /**
     * @dataProvider             Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv3\Exceptions\RuntimeException
     * @expectedExceptionMessage ERROR_CMD_FILE_NOTEXIST
     */
    public function testRenameFailed($adapter)
    {
        $this->assertTrue($adapter->rename('foo/notexist.md', 'foo/rename.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testCopy($adapter)
    {
        $this->assertTrue($adapter->copy('foo/bar.md', 'foo/copy.md'));
    }

    /**
     * @dataProvider             Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv3\Exceptions\RuntimeException
     * @expectedExceptionMessage ERROR_CMD_FILE_NOTEXIST
     */
    public function testCopyFailed($adapter)
    {
        $this->assertTrue($adapter->copy('foo/notexist.md', 'foo/copy.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testDelete($adapter)
    {
        $this->assertTrue($adapter->delete('foo/rename.md'));
    }

    /**
     * @dataProvider             Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv3\Exceptions\RuntimeException
     * @expectedExceptionMessage ERROR_CMD_FILE_NOTEXIST
     */
    public function testDeleteFailed($adapter)
    {
        $this->assertTrue($adapter->delete('foo/notexist.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testCreateDir($adapter)
    {
        $this->assertArrayHasKey('ctime', $adapter->createDir('bar', new Config()));
    }

    /**
     * @dataProvider             Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv3\Exceptions\RuntimeException
     * @expectedExceptionMessage ERROR_CMD_COS_PATH_CONFLICT
     */
    public function testCreateDirFailed($adapter)
    {
        $this->assertArrayHasKey('ctime', $adapter->createDir('bar', new Config()));
    }

    /**
     * @dataProvider Provider
     */
    public function testDeleteDir($adapter)
    {
        $this->assertTrue($adapter->deleteDir('bar'));
    }

    /**
     * @dataProvider             Provider
     * @expectedException \Freyo\Flysystem\QcloudCOSv3\Exceptions\RuntimeException
     * @expectedExceptionMessage ERROR_CMD_FILE_NOTEXIST
     */
    public function testDeleteDirFailed($adapter)
    {
        $this->assertTrue($adapter->deleteDir('notexist'));
    }

    /**
     * @dataProvider Provider
     */
    public function testSetVisibility($adapter)
    {
        $this->assertTrue($adapter->setVisibility('foo/copy.md', 'private'));
    }

    /**
     * @dataProvider Provider
     */
    public function testHas($adapter)
    {
        $this->assertTrue($adapter->has('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testRead($adapter)
    {
        $this->assertSame(['contents' => 'newcontent'], $adapter->read('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetUrl($adapter, $config)
    {
        $this->assertSame(
            $config['protocol'] . '://' . $config['domain'] . '/foo/bar.md',
            $adapter->getUrl('foo/bar.md')
        );
    }

    /**
     * @dataProvider Provider
     */
    public function testReadStream($adapter)
    {
        $this->assertSame(
            stream_get_contents(fopen($adapter->getUrl('foo/bar.md'), 'r')),
            stream_get_contents($adapter->readStream('foo/bar.md')['stream'])
        );
    }

    /**
     * @dataProvider Provider
     */
    public function testListContents($adapter)
    {
        $this->assertArrayHasKey('infos', $adapter->listContents('foo'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetMetadata($adapter)
    {
        $this->assertArrayHasKey('access_url', $adapter->getMetadata('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetSize($adapter)
    {
        $this->assertSame(['size' => strlen('newcontent')], $adapter->getSize('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetMimetype($adapter)
    {
        $this->assertNotSame(['mimetype' => ''], $adapter->getMimetype('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetTimestamp($adapter)
    {
        $this->assertNotSame(['timestamp' => 0], $adapter->getTimestamp('foo/bar.md'));
    }

    /**
     * @dataProvider Provider
     */
    public function testGetVisibility($adapter)
    {
        $this->assertSame(['visibility' => 'private'], $adapter->getVisibility('foo/copy.md'));
    }
}