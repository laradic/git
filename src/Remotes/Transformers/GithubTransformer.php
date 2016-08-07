<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Remotes\Transformers;

use Laradic\Git\Remotes\TransformerInterface;

/**
 * Transforms github output into the common structure
 *
 * @package        Laradic\Git
 * @author         Laradic
 * @copyright      Copyright (c) 2015, Laradic. All rights reserved
 */
class GithubTransformer implements TransformerInterface
{

    public function transform($data, $functionName)
    {

        if (method_exists($this, $functionName)) {
            return call_user_func_array([ $this, $functionName ], [ $data ]);
        } else {
            return $data;
        }
    }


    public function getUser($data)
    {
        return [
            'username' => $data[ 'login' ],
            'avatar'   => $data[ 'avatar_url' ]
        ];
    }

    public function getUsername($data)
    {
        return $data;
    }

    public function listWebhooks($data)
    {
        $hooks = [ ];
        foreach ($data as $hook) {
            $hooks[] = $this->getWebhook($hook);
        }

        return $hooks;
    }

    public function getWebhook($data)
    {
        return [
            'config' => $data[ 'config' ],
            'active' => $data[ 'active' ],
            'events' => $data[ 'events' ],
            'name'   => $data[ 'name' ]
        ];
    }

    public function createWebhook($data)
    {
        return $data;
    }

    public function removeWebhook($data)
    {
        return $data;
    }

    public function listOrganisations($data)
    {
        return collect($data)->pluck('login')->toArray();
    }

    public function listRepositories($data)
    {
        return collect($data)->pluck('name')->toArray();
    }

    public function createRepository($data)
    {
        return $data;
    }

    public function deleteRepository($data)
    {
        return $data;
    }

    public function getBranch($data)
    {
        return [ 'name' => $data[ 'name' ], 'sha' => array_get($data, 'commit.sha') ];
    }

    public function getBranches($data)
    {
        return array_combine(collect($data)->pluck('name')->toArray(), collect($data)->pluck('commit.sha')->toArray());
    }

    public function getMainBranch($data)
    {
        return $data;
    }

    public function getRepositoryManifest($data)
    {
        return $data;
    }

    public function getTags($data)
    {
        return array_combine(collect($data)->pluck('name')->toArray(), collect($data)->pluck('commit.sha')->toArray());
    }

    public function getRaw($data)
    {
        if (is_array($data) && array_key_exists('content', $data)) {
            return base64_decode(array_get($data, 'content'));
        }

        return '';
    }

    public function getChangeset($data)
    {
        return $data;
    }

    public function getRepositoryCommits($data)
    {
        return $data;
    }

    public function getBranchCommits($data)
    {
        return collect($data)->transform(function($ghc){
            return [
                'ref' => $ghc['sha'],
                'message' => $ghc['commit']['message'],
                'date' => date_create($ghc['commit']['committer']['date']),
                'author' => [
                    'name' => $ghc['commit']['author']['name'],
                    'username' => $ghc['author']['login'],
                    'avatar' => $ghc['author']['avatar_url']
                ],
                'url' => $ghc['html_url']
            ];
        })->toArray();
    }
}
