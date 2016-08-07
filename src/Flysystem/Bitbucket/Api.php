<?php
namespace Laradic\Git\Flysystem\Bitbucket;

use Bitbucket\API\Http\Listener;
use Buzz\Message\MessageInterface;
use League\Flysystem\Util\MimeType;
use Laradic\Git\Exceptions\LaradicGitException;
use Laradic\Git\Flysystem\ApiInterface;
use Laradic\Git\Flysystem\SettingsInterface;
use Laradic\Git\Manager;
use Laradic\Support\Arr;
use Laradic\Support\Path;
use Laradic\Support\Str;

/**
 * Facade class for the Github Api Library
 */
class Api implements ApiInterface
{
    /**
     * @var \Bitbucket\API\Api
     */
    protected $bbApi;

    /** @var SettingsInterface */
    protected $settings;

    /** @var bool */
    protected $isAuthenticationAttempted = false;

    public function getApi($name)
    {
        $this->authenticate();

        return $this->bbApi->api($name);
    }

    public function __construct(\Bitbucket\API\Api $bbApi, SettingsInterface $settings)
    {

        /* @NOTE: If $settings contains `credentials` but not an `author` we are
         * still in `read-only` mode.
         */
        $this->bbApi    = $bbApi;
        $this->settings = $settings;
    }

    protected function authenticate()
    {
        if ( $this->isAuthenticationAttempted === false )
        {
            $credentials = collect($this->settings->getCredentials());
            $auth        = $credentials->get('auth', Manager::AUTH_BASIC);

            if ( $auth === Manager::AUTH_OAUTH )
            {
                $listener = new Listener\OAuthListener([
                    'oauth_consumer_key'    => $credentials->get('key'),
                    'oauth_consumer_secret' => $credentials->get('secret'),
                ]);
            }
            elseif ( $auth === Manager::AUTH_BASIC )
            {
                $listener = new Listener\BasicAuthListener($credentials->get('username'), $credentials->get('password'));
            }
            elseif ( $auth === Manager::AUTH_OAUTH2 )
            {
                $listener = new Listener\OAuth2Listener([
                    'oauth_consumer_id'     => $credentials->get('key'),
                    'oauth_consumer_secret' => $credentials->get('secret'),
                ]);
            }
            elseif ( $auth === Manager::AUTH_TOKEN )
            {
                throw LaradicGitException::credentialTypeNotSupported($auth);
            }
            else
            {
                $listener = new Listener\OAuthListener([
                    'oauth_consumer_key'    => $credentials->get('key'),
                    'oauth_consumer_secret' => $credentials->get('secret'),
                ]);
            }

            $this->bbApi->getClient()->addListener($listener);

            $this->isAuthenticationAttempted = true;
        }
    }

    protected function getUsername()
    {
        $user = $this->_content($this->getApi('User')->get());

        return $user[ 'user' ][ 'username' ];
    }

    protected function _content(MessageInterface $m, $assoc = true)
    {
        return json_decode($m->getContent(), $assoc);
    }

    protected function getSettings()
    {
        return [ $this->settings->getVendor(), $this->settings->getPackage(), $this->settings->getBranch(), $this->settings->getReference() ];
    }


    /**
     * @param string $path
     *
     * @return bool
     */
    public function exists($path)
    {
        list($vendor, $package, $branch, $ref) = $this->getSettings();

        $dir   = Path::getDirectory($path);
        $data  = $this->_content($this->getApi('Repositories\Src')->get($vendor, $package, $branch, $dir));
        $files = Arr::pluck($data[ 'files' ], 'path');

        return in_array($path, $files, false);
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
        list($vendor, $package, $branch, $ref) = $this->getSettings();

        return $this->getApi('Repositories\Src')->raw($vendor, $package, $branch, $path)->getContent();
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function getLastUpdatedTimestamp($path)
    {
        list($vendor, $package, $branch, $ref) = $this->getSettings();
        $c    = $this->getApi('Repositories\Repository')->filehistory($vendor, $package, $branch, $path)->getContent();
        $data = json_decode($c, true);
        foreach ( $data as $d )
        {
            foreach ( $d[ 'files' ] as $file )
            {
                if ( $file[ 'file' ] === $path && $file[ 'type' ] === 'modified' )
                {
                    return $d;
                }
            }
        }
    }

    protected function getAllDirectories($path, array &$dirs)
    {
        list($vendor, $package, $branch, $ref) = $this->getSettings();


        $get  = $this->getApi('Repositories\Src')->get($vendor, $package, $branch, $path)->getContent();
        $data = json_decode($get, true);
        if ( !isset($data[ 'directories' ]) )
        {
            return [ ];
        }

        foreach ( $data[ 'directories' ] as $subdir )
        {
            $dir    = Path::join($path, $subdir);
            $dirs[] = [ 'path' => $dir, 'type' => 'dir' ];
            $this->getAllDirectories($dir, $dirs);
        }
    }


    public function listContents($path = '/', $recursive = false)
    {
        list($vendor, $package, $branch, $ref) = $this->getSettings();
        $response = [ ];
        if ( $recursive )
        {
            $this->authenticate();

            $get  = $this->bbApi->getClient()->get("repositories/{$vendor}/{$package}/directory")->getContent();
            $data = json_decode($get, true);
            foreach ( $data[ 'values' ] as $path )
            {
                $isFile = Path::hasExtension($path);
                if ( $path === '/' )
                {
                    continue;
                }
                $response[] = [ 'type' => $isFile ? 'file' : 'dir', 'path' => Str::removeRight($path, '/') ];
            }
        }
        else
        {
            $get = $this->getApi('Repositories\Src')->get($vendor, $package, $branch, $path)->getContent();

            $data = json_decode($get, true);

            foreach ( $data[ 'files' ] as $file )
            {
                $response[] = array_merge($file, [ 'type' => 'file' ]);
            }
            foreach ( $data[ 'directories' ] as $dire )
            {
                $response[] = [ 'type' => 'dir', 'path' => $dire ];
            }
        }


        return $response;
    }

    /**
     * @param string $path
     *
     * @return array|bool
     */
    public function getMetaData($path)
    {
        list($vendor, $package, $branch, $ref) = $this->getSettings();
        $get  = $this->getApi('Repositories\Src')->get($vendor, $package, $branch, $path)->getContent();
        $data = json_decode($get, true);
//        if ($isFile) {
//            $response = [
//                'type' => 'file',
//                'path' => $path
//            ];
//            $response[ 'contents' ] = $this->getApi('Repositories\Src')->raw($vendor, $package, $branch, $path)->getContent();
//            foreach ($data[ 'files' ] as $file) {
//                if ($path === $file[ 'path' ]) {
//                    $response[ 'size' ]      = $file[ 'size' ];
//                    $response[ 'timestamp' ] = $file[ 'timestamp' ];
//                    break;
//                }
//            }
//        } else {
        $response = [ ];
        foreach ( $data[ 'files' ] as $file )
        {
            $response[] = array_merge($file, [ 'type' => 'file' ]);
        }
        foreach ( $data[ 'directories' ] as $dire )
        {
            $response[] = [ 'type' => 'dir', 'path' => $dire ];
        }
//
//        }

        return $response;
    }

    public function getAllDirs($path)
    {

        $list = [ ];
        $this->getAllDirectories($path, $list);

        return $list;
    }


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

    /**
     * @param string $path
     * @param bool   $recursive
     *
     * @return array
     */
    public function getRecursiveMetadata($path, $recursive)
    {
        // TODO: Implement getRecursiveMetadata() method.
    }
}
