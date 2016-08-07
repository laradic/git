<?php

namespace Laradic\Git\Flysystem\Bitbucket;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\StreamedCopyTrait;
use League\Flysystem\Adapter\Polyfill\StreamedTrait;
use League\Flysystem\Config;
use League\Flysystem\Util;
use Laradic\Git\Flysystem\ApiInterface;

/**
 *
 */
class BitbucketAdapter extends AbstractAdapter
{
    use StreamedTrait, StreamedCopyTrait;

    /** @var ApiInterface */
    private $api;

    /**
     * @return ApiInterface
     */
    final public function getApi()
    {
        return $this->api;
    }

    /**
     * @param ApiInterface $api
     */
    public function __construct(ApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * Check that a file or directory exists in the repository
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        return $this->getApi()->exists($path);
    }

    /**
     * Read a file
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        return [ApiInterface::KEY_CONTENTS => $this->getApi()->getFileContents($path)];
    }

    /**
     * List contents of a directory.
     *
     * @param string $path
     * @param bool $recursive
     *
     * @return array
     */
    public function listContents($path = '/', $recursive = false)
    {
        return $this->getApi()->listContents($path, $recursive);
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        return $this->getApi()->getMetaData($path);
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        return $this->getApi()->getMetaData($path);
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        return ['mimetype' => $this->getApi()->guessMimeType($path)];
    }

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        return $this->getApi()->getLastUpdatedTimestamp($path);
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        $recursive = false;
        $metadata = $this->getApi()->getRecursiveMetadata($path, $recursive);
        return $metadata[0];
    }


    /**
     * @inheritdoc
     */
    public function write($path, $contents, Config $config)
    {
        $type = 'file';
        $result = compact('contents', 'type', 'size', 'path');

        if ($visibility = $config->get('visibility')) {
            $result['visibility'] = $visibility;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function update($path, $contents, Config $config)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newpath)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function createDir($dirname, Config $config)
    {
        return ['path' => $dirname, 'type' => 'dir'];
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($dirname)
    {
        return false;
    }
}

/*EOF*/
