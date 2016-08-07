<?php
namespace Laradic\Git\Flysystem\Github;

use Github\Api\GitData;
use Github\Client;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Util\MimeType;
use RuntimeException;
use Laradic\Git\Exceptions\LaradicGitException;
use Laradic\Git\Flysystem\ApiInterface;
use Laradic\Git\Flysystem\SettingsInterface;
use Laradic\Git\Manager;


/**
 * This is the class Api.
 * @package        Laradic\Git
 * @author         Potherca
 * @copyright      Copyright (c) 2015 Potherca <potherca@gmail.com>
 */
class Api implements ApiInterface
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\
    const ERROR_NOT_FOUND = 'Not Found';

    const API_GIT_DATA = 'git';
    const API_REPO = 'repo';

    const KEY_BLOB = 'blob';
    const KEY_DIRECTORY = 'dir';
    const KEY_FILE = 'file';
    const KEY_FILENAME = 'basename';
    const KEY_MODE = 'mode';
    const KEY_NAME = 'name';
    const KEY_PATH = 'path';
    const KEY_SHA = 'sha';
    const KEY_SIZE = 'size';
    const KEY_STREAM = 'stream';
    const KEY_TIMESTAMP = 'timestamp';
    const KEY_TREE = 'tree';
    const KEY_TYPE = 'type';
    const KEY_VISIBILITY = 'visibility';
    const ERROR_NO_NAME = 'Could not set name for entry';

    /** @var Client */
    protected $client;

    /** @var SettingsInterface */
    protected $settings;

    /** @var bool */
    protected $isAuthenticationAttempted = false;

    /**
     * getClient method
     * @return \Github\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    //////////////////////////// SETTERS AND GETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @param string $name
     *
     * @return \Github\Api\ApiInterface
     */
    public function getApi($name)
    {
        $this->authenticate();
        return $this->client->api($name);
    }

    /**
     * @return GitData
     */
    protected function getGitDataApi()
    {
        return $this->getApi(self::API_GIT_DATA);
    }

    /**
     * @return \Github\Api\Repo
     */
    protected function getRepositoryApi()
    {
        return $this->getApi(self::API_REPO);
    }

    /**
     * @return \Github\Api\Repository\Contents
     */
    protected function getRepositoryContent()
    {
        return $this->getRepositoryApi()->contents();
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function __construct(Client $client, SettingsInterface $settings)
    {
        /* @NOTE: If $settings contains `credentials` but not an `author` we are
         * still in `read-only` mode.
         */

        $this->client   = $client;
        $this->settings = $settings;
    }

    /**
     *
     * @throws \Github\Exception\InvalidArgumentException
     */
    protected function authenticate()
    {
        if ( $this->isAuthenticationAttempted === false )
        {
            $credentials = collect($this->settings->getCredentials());
            $auth   = $credentials->get('auth', Manager::AUTH_BASIC);

            if ( $auth === Manager::AUTH_OAUTH2 )
            {
                $this->client->authenticate($credentials->get('key'), $credentials->get('secret'), Client::AUTH_URL_CLIENT_ID);
            }
            elseif ( $auth === Manager::AUTH_TOKEN )
            {
                $this->client->authenticate($credentials->get('secret'), null, Client::AUTH_HTTP_TOKEN);
            }
            elseif ( $auth === Manager::AUTH_BASIC )
            {
                $this->client->authenticate(
                    $credentials->get('username', $credentials->get('key')),
                    $credentials->get('password', $credentials->get('secret')),
                    Client::AUTH_HTTP_PASSWORD
                );
            }
            elseif ( $auth === Manager::AUTH_OAUTH )
            {
                throw LaradicGitException::credentialTypeNotSupported($auth);
            }
            else
            {
                $this->client->authenticate($credentials->get('secret'), null, Client::AUTH_HTTP_TOKEN);
            }

            $this->isAuthenticationAttempted = true;
        }
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function exists($path)
    {
        return $this->getRepositoryContent()->exists(
            $this->settings->getVendor(),
            $this->settings->getPackage(),
            $path,
            $this->settings->getReference()
        );
    }

    /**
     * @param $path
     *
     * @return null|string
     *
     * @throws \Github\Exception\ErrorException
     */
    public function getFileContents($path)
    {
        return $this->getRepositoryContent()->download(
            $this->settings->getVendor(),
            $this->settings->getPackage(),
            $path,
            $this->settings->getReference()
        );
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function getLastUpdatedTimestamp($path)
    {
        $commits = $this->commitsForFile($path);

        $updated = array_shift($commits);

        $time = new \DateTime($updated[ 'commit' ][ 'committer' ][ 'date' ]);

        return [ 'timestamp' => $time->getTimestamp() ];
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function getCreatedTimestamp($path)
    {
        $commits = $this->commitsForFile($path);

        $created = array_pop($commits);

        $time = new \DateTime($created[ 'commit' ][ 'committer' ][ 'date' ]);

        return [ 'timestamp' => $time->getTimestamp() ];
    }

    /**
     * @param string $path
     *
     * @return array|bool
     */
    public function getMetaData($path)
    {
        try
        {
            $metadata = $this->getRepositoryContent()->show(
                $this->settings->getVendor(),
                $this->settings->getPackage(),
                $path,
                $this->settings->getReference()
            );
        }
        catch (RuntimeException $exception)
        {
            if ( $exception->getMessage() === self::ERROR_NOT_FOUND )
            {
                $metadata = false;
            }
            else
            {
                throw $exception;
            }
        }

        return $metadata;
    }

    /**
     * @param string $path
     * @param bool   $recursive
     *
     * @return array
     */
    public function getRecursiveMetadata($path, $recursive)
    {
        // If $info['truncated'] is `true`, the number of items in the tree array
        // exceeded the github maximum limit. If you need to fetch more items,
        // multiple calls will be needed

        $info = $this->getGitDataApi()->trees()->show(
            $this->settings->getVendor(),
            $this->settings->getPackage(),
            $this->settings->getReference(),
            $recursive
        );

        $treeMetadata = $this->extractMetaDataFromTreeInfo($info[ self::KEY_TREE ], $path, $recursive);

        return $this->normalizeTreeMetadata($treeMetadata);
    }


    /**
     * @param string $path
     *
     * @return null|string
     */
    public function guessMimeType($path)
    {
        //@NOTE: The github API does not return a MIME type, so we have to guess :-(
        if ( strrpos($path, '.') > 1 )
        {
            $extension = substr($path, strrpos($path, '.') + 1);
            $mimeType  = MimeType::detectByFileExtension($extension) ?: 'text/plain';
        }
        else
        {
            $content  = $this->getFileContents($path);
            $mimeType = MimeType::detectByContent($content);
        }

        return $mimeType;
    }
    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /**
     * @param array  $tree
     * @param string $path
     * @param bool   $recursive
     *
     * @return array
     */
    protected function extractMetaDataFromTreeInfo(array $tree, $path, $recursive)
    {
        if ( empty($path) === false )
        {
            $metadata = array_filter($tree, function ($entry) use ($path, $recursive)
            {
                $match = false;

                if ( strpos($entry[ self::KEY_PATH ], $path) === 0 )
                {
                    if ( $recursive === true )
                    {
                        $match = true;
                    }
                    else
                    {
                        $length = strlen($path);
                        $match  = (strpos($entry[ self::KEY_PATH ], '/', $length) === false);
                    }
                }

                return $match;
            });
        }
        elseif ( $recursive === false )
        {
            $metadata = array_filter($tree, function ($entry) use ($path)
            {
                return (strpos($entry[ self::KEY_PATH ], '/', strlen($path)) === false);
            });
        }
        else
        {
            $metadata = $tree;
        }

        return $metadata;
    }

    /**
     * @param $permissions
     *
     * @return string
     */
    protected function guessVisibility($permissions)
    {
        return $permissions & 0044 ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE;
    }

    /**
     * @param array $metadata
     *
     * @return array
     */
    protected function normalizeTreeMetadata($metadata)
    {
        $result = [ ];

        if ( is_array(current($metadata)) === false )
        {
            $metadata = [ $metadata ];
        }

        foreach ( $metadata as $entry )
        {
            $this->setEntryName($entry);
            $this->setEntryType($entry);
            $this->setEntryVisibility($entry);

            $this->setDefaultValue($entry, self::KEY_CONTENTS);
            $this->setDefaultValue($entry, self::KEY_STREAM);
            $this->setDefaultValue($entry, self::KEY_TIMESTAMP);


            $result[] = $entry;
        }

        return $result;
    }

    /**
     * @param $path
     *
     * @return array
     */
    protected function commitsForFile($path)
    {
        return $this->getRepositoryApi()->commits()->all(
            $this->settings->getVendor(),
            $this->settings->getPackage(),
            [
                'sha'  => $this->settings->getBranch(),
                'path' => $path,
            ]
        );
    }

    /**
     * @param array  $entry
     * @param string $key
     * @param bool   $default
     *
     * @return mixed
     */
    protected function setDefaultValue(array &$entry, $key, $default = false)
    {
        if ( isset($entry[ $key ]) === false )
        {
            $entry[ $key ] = $default;
        }
    }

    /**
     * @param $entry
     */
    protected function setEntryType(&$entry)
    {
        if ( isset($entry[ self::KEY_TYPE ]) === true )
        {
            switch ( $entry[ self::KEY_TYPE ] )
            {
                case self::KEY_BLOB:
                    $entry[ self::KEY_TYPE ] = self::KEY_FILE;
                    break;

                case self::KEY_TREE:
                    $entry[ self::KEY_TYPE ] = self::KEY_DIRECTORY;
                    break;
            }
        }
    }

    /**
     * @param $entry
     */
    protected function setEntryVisibility(&$entry)
    {
        if ( isset($entry[ self::KEY_MODE ]) )
        {
            $entry[ self::KEY_VISIBILITY ] = $this->guessVisibility($entry[ self::KEY_MODE ]);
        }
        else
        {
            $entry[ self::KEY_VISIBILITY ] = false;
        }
    }

    /**
     * @param $entry
     */
    protected function setEntryName(&$entry)
    {
        if ( isset($entry[ self::KEY_NAME ]) === false )
        {
            if ( isset($entry[ self::KEY_FILENAME ]) === true )
            {
                $entry[ self::KEY_NAME ] = $entry[ self::KEY_FILENAME ];
            }
            elseif ( isset($entry[ self::KEY_PATH ]) === true )
            {
                $entry[ self::KEY_NAME ] = $entry[ self::KEY_PATH ];
            }
            else
            {
                $entry[ self::KEY_NAME ] = null;
            }
        }
    }

}
