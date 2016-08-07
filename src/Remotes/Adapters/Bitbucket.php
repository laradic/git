<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Remotes\Adapters;

use Bitbucket\API\Api;
use Bitbucket\API\Http\Listener;
use Buzz\Message\MessageInterface;
use Laradic\Git\Exceptions\LaradicGitException;
use Laradic\Git\Manager;
use Laradic\Git\Remotes\AdapterInterface;

/**
 * This is the class BitbucketRemote.
 *
 * @author    Laradic
 * @copyright Copyright (c) 2015, Laradic. All rights reserved
 */
class Bitbucket extends AbstractAdapter implements AdapterInterface
{
    const DRIVER = 'bitbucket';

    protected $username;

    /**
     * @var Api
     */
    protected $api;


    public function connect($config)
    {
        $config      = collect($config);
        $this->api   = $config->get('api', new Api());
        $type        = $config->get('auth', Manager::AUTH_BASIC);

        if ( $type === Manager::AUTH_OAUTH ) {
            $listener = new Listener\OAuthListener([
                'oauth_consumer_key'    => $config->get('key'),
                'oauth_consumer_secret' => $config->get('secret'),
            ]);
        } elseif ( $type === Manager::AUTH_BASIC ) {
            $listener = new Listener\BasicAuthListener(
                $config->get('username', $config->get('key')),
                $config->get('password', $config->get('secret'))
            );
        } elseif ( $type === Manager::AUTH_OAUTH2 ) {
            $listener = new Listener\OAuth2Listener([
                'oauth_consumer_id'     => $config->get('key'),
                'oauth_consumer_secret' => $config->get('secret'),
            ]);
        } elseif ( $type === Manager::AUTH_TOKEN ) {
            throw LaradicGitException::credentialTypeNotSupported($type);
        } else {
            $listener = new Listener\OAuthListener([
                'oauth_consumer_key'    => $config->get('key'),
                'oauth_consumer_secret' => $config->get('secret'),
            ]);
        }

        $this->api->getClient()->addListener($listener);
    }

    /**
     * getUser method
     *
     * @return array
     */
    public function getUser()
    {
        return $this->_content($this->api->api('User')->get());
    }

    protected function _content(MessageInterface $m, $assoc = true)
    {
        return json_decode($m->getContent(), $assoc);
    }

    /**
     * getUsername method
     *
     * @return string
     */
    public function getUsername()
    {

        return $this->_content($this->api->api('User')->get())[ 'user' ][ 'username' ];
    }

    public function listWebhooks($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Hooks')->all($owner, $repo));
    }

    public function getWebhook($repo, $uuid, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Hooks')->get($owner, $repo, $uuid));
    }

    public function createWebhook($repo, array $data, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Hooks')->create($owner, $repo, $data));
    }

    public function removeWebhook($repo, $uuid, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Hooks')->delete($owner, $repo, $uuid));
    }

    public function listOrganisations($owner = null)
    {
        $this->owner($owner);

        $p = $this->_content($this->api->api('User')->privileges());

        if ( !isset($p[ 'teams' ]) ) {
            return [ ];
        }

        return array_keys($p[ 'teams' ]);
    }

    public function listRepositories($owner = null)
    {
        $this->owner($owner);
        $r = $this->_content($this->api->api('User')->repositories()->get());

        return collect($r)->where('owner', $owner)->toArray(); //collect($r)->filter('owner', $owner, false)->toArray();

        #return $r; // collect($r)->transform(function($item){ return $item['slug']; })->all();
    }

    public function createRepository($repo, array $data = [ ], $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Repository')->create($owner, $repo, $data));
    }

    public function deleteRepository($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Repository')->delete($owner, $repo));
    }

    /**
     * getBranch method
     *
     * @todo get the correct branch
     *
     * @param      $repo
     * @param      $ref
     * @param null $owner
     *
     * @return mixed
     */
    public function getBranch($repo, $ref, $owner = null)
    {
        $this->owner($owner);

        return $this->getBranches($repo, $owner)[ $ref ];
    }

    public function getBranches($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Repository')->branches($owner, $repo));
    }

    public function getMainBranch($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Repository')->branch($owner, $repo));
    }

    public function getRepositoryManifest($repo, $ref, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Repository')->manifest($owner, $repo, $ref));
    }

    public function getTags($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Repository')->tags($owner, $repo));
    }

    public function getRaw($repo, $ref, $path, $owner = null)
    {
        $this->owner($owner);

        return $this->api->api('Repositories\Repository')->raw($owner, $repo, $ref, $path)->getContent();
    }

    public function getChangeset($repo, $ref, $path, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Repository')->filehistory($owner, $repo, $ref, $path));
    }

    public function getRepositoryCommits($repo, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Commits')->all($owner, $repo));
    }

    public function getBranchCommits($repo, $branch, $owner = null)
    {
        $this->owner($owner);

        return $this->_content($this->api->api('Repositories\Commits')->all($owner, $repo, compact('branch')));
    }

    public function getContentList($path, $repo, $ref, $owner = null)
    {
        $this->owner($owner);
        return $this->_content($this->api->api('Repositories\Src')->get($owner, $repo, $ref, $path));
    }

    public function downloadArchive($repo, $ref, $localPath, $owner = null)
    {
        $client = new \GuzzleHttp\Client([]);
        $client->request('GET', "https://bitbucket.org/{$owner}/{$repo}/get/{$ref}.zip", [
            'sink' => $localPath
        ]);
        return $localPath;
    }
}
