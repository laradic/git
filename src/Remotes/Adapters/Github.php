<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Remotes\Adapters;

use Github\Client as Api;
use Laradic\Filesystem\Filesystem;
use Laradic\Git\Exceptions\LaradicGitException;
use Laradic\Git\Manager;
use Laradic\Git\Remotes\AdapterInterface;

/**
 * This is the class BitbucketRemote.
 *
 * @author    Laradic
 * @copyright Copyright (c) 2015, Laradic. All rights reserved
 */
class Github extends AbstractAdapter implements AdapterInterface
{
    const DRIVER = 'github';

    /**
     * @var \Github\Client
     */
    protected $api;

    public function connect($config)
    {
        $config    = collect($config);
        $this->api = $config->get('api', new Api());
        $type      = $config->get('auth', Manager::AUTH_BASIC);

        if ( $type === Manager::AUTH_OAUTH2 ) {
            $this->api->authenticate($config->get('key'), $config->get('secret'), Api::AUTH_URL_CLIENT_ID);
        } elseif ( $type === Manager::AUTH_TOKEN ) {
            $this->api->authenticate($config->get('secret'), null, Api::AUTH_HTTP_TOKEN);
        } elseif ( $type === Manager::AUTH_BASIC ) {
            $this->api->authenticate(
                $config->get('username', $config->get('key')),
                $config->get('password', $config->get('secret')),
                Api::AUTH_HTTP_PASSWORD
            );
        } elseif ( $type === Manager::AUTH_OAUTH ) {
            throw LaradicGitException::credentialTypeNotSupported($type);
        } else {
            $this->api->authenticate($config->get('secret'), null, Api::AUTH_HTTP_TOKEN);
        }
    }

    /**
     * getUser method
     *
     * @return array
     */
    public function getUser()
    {
        return $this->api->currentUser()->show();
    }

    /**
     * listWebhooks method
     *
     * @param      $repo
     * @param null $owner
     *
     * @return mixed
     */
    public function listWebhooks($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repo()->hooks()->all($owner, $repo);
    }

    public function getWebhook($repo, $uuid, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repo()->hooks()->show($owner, $repo, $uuid);
    }

    public function createWebhook($repo, array $data, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repo()->hooks()->create($owner, $repo, $data);
    }

    public function removeWebhook($repo, $uuid, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repo()->hooks()->remove($owner, $repo, $uuid);
    }

    public function listOrganisations($owner = null)
    {
        $this->owner($owner);

        return $this->api->user()->organizations($owner);
    }

    public function listRepositories($owner = null)
    {
        if ( is_null($owner) ) {
            return $this->api->user()->repositories($this->getUsername());
        } else {
            if ( $owner === $this->getUsername() ) {
                return $this->api->repo()->all();
            } else {
                return $this->api->organization()->repositories($owner);
            }
        }
    }

    /**
     * getUsername method
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->api->currentUser()->show()[ 'login' ];
    }

    public function createRepository($repo, array $data = [ ], $owner = null)
    {
        return $this->api->repositories()->create($repo, '', '', $data[ 'private' ] === false, $this->getUsername() == $owner ? null : $owner);
    }

    public function deleteRepository($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repositories()->remove($owner, $repo);
    }

    public function getBranches($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repositories()->branches($owner, $repo);
    }

    /**
     * getMainBranch method
     *
     * @todo fix so it gets the actual main branch
     *
     * @param      $repo
     * @param null $owner
     *
     * @return array
     */
    public function getMainBranch($repo, $owner = null)
    {
        return $this->getBranch($repo, 'master', $owner);
    }

    public function getBranch($repo, $ref, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repositories()->branches($owner, $repo, $ref);
    }

    public function getRepositoryManifest($repo, $ref, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repositories()->show($owner, $repo);
    }

    public function getTags($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repositories()->tags($owner, $repo);
    }

    public function getRaw($repo, $ref, $path, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repositories()->contents()->show($owner, $repo, $path, $ref);
    }

    /**
     * getChangeset method
     *
     * @todo make this working
     *
     * @param      $repo
     * @param      $ref
     * @param      $path
     * @param null $owner
     *
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    public function getChangeset($repo, $ref, $path, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repositories()->commits()->all($owner, $repo, [ 'path' => $path, 'sha' => $ref ]);
    }

    public function getRepositoryCommits($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repositories()->commits()->all($owner, $repo, [ ]);
    }

    public function getBranchCommits($repo, $branch, $owner = null)
    {
        $this->owner($owner);

        return $this->api->repositories()->commits()->all($owner, $repo, [ 'sha' => $branch ]);
    }

    public function getContentList($path, $repo, $ref, $owner = null)
    {
        // TODO: Implement getContentList() method.
    }

    public function downloadArchive($repo, $ref, $localPath, $owner = null)
    {
        $this->owner($owner);
        Filesystem::create()->put($localPath, $this->api->repo()->contents()->archive($owner, $repo, 'zipball', $ref));
        return $localPath;
    }
}
