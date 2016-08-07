<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Remotes\Transformers;

use Laradic\Git\Remotes\TransformerInterface;

/**
 * This is the class NullTransformer.
 *
 * @package        Laradic\Git
 * @author         Laradic
 * @copyright      Copyright (c) 2015, Laradic. All rights reserved
 */
class NullTransformer implements TransformerInterface
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
            'username' => $data['login'],
            'avatar' => $data['avatar_url']
        ];
    }

    public function getUsername($data)
    {
        return $data;
    }

    public function listWebhooks($data)
    {
        return $data;
    }

    public function getWebhook($data)
    {
        return $data;
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
        return $data;
    }

    public function listRepositories($data)
    {
        return $data;
    }

    public function createRepository($data)
    {
        return $data;
    }

    public function deleteRepository($data)
    {
        return $data;
    }

    public function getBranches($data)
    {
        return $data;
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
        return $data;
    }

    public function getRaw($data)
    {
        return $data;
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
        return $data;
    }
}
