<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Remotes\Transformers;

use Laradic\Git\Remotes\TransformerInterface;

/**
 * Transforms bitbucket output into the common structure
 *
 * @package        Laradic\Git
 * @author         Laradic
 * @copyright      Copyright (c) 2015, Laradic. All rights reserved
 */
class BitbucketTransformer implements TransformerInterface
{

    public function transform($data, $functionName)
    {

        if ( method_exists($this, $functionName) ) {
            return call_user_func_array([ $this, $functionName ], [ $data ]);
        } else {
            return $data;
        }
    }


    public function getUser($data)
    {
        return [
            'username' => $data[ 'user' ][ 'username' ],
            'avatar'   => $data[ 'user' ][ 'avatar' ],
        ];
    }

    public function getUsername($data)
    {
        return $data;
    }

    public function listWebhooks($data)
    {
        $hooks = [ ];
        if ( isset($data[ 'values' ]) ) {
            foreach ( $data[ 'values' ] as $hook ) {
                $hooks[] = $this->getWebhook($hook);
            }
        }

        return $hooks;
    }

    public function getWebhook($data)
    {
        return [
            'config' => array_replace_recursive($data[ 'subject' ], [ 'url' => $data[ 'url' ] ]),
            'active' => $data[ 'active' ],
            'events' => $data[ 'events' ],
            'name'   => $data[ 'description' ],
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
        return $data;
    }

    public function listRepositories($data)
    {
        return collect($data)->pluck('slug')->toArray();
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
        return [ 'name' => $data[ 'branch' ], 'sha' => $data[ 'raw_node' ] ];
    }

    public function getBranches($data)
    {
        return array_combine(array_keys($data), collect($data)->pluck('raw_node')->toArray());
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
        return array_combine(array_keys($data), collect($data)->pluck('raw_node')->toArray());
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
        if(!isset($data[ 'values' ])){
            return [];
        }
        return collect($data[ 'values' ])->transform(function ($bbc) {
            return [
                'ref'     => $bbc[ 'hash' ],
                'message' => $bbc[ 'message' ],
                'date'    => date_create($bbc[ 'date' ]),
                'author'  => [
                    'name'     => $bbc[ 'author' ][ 'user' ][ 'display_name' ],
                    'username' => $bbc[ 'author' ][ 'user' ][ 'username' ],
                    'avatar'   => $bbc[ 'author' ][ 'user' ][ 'links' ][ 'avatar' ][ 'href' ],
                ],
                'url'     => $bbc[ 'links' ][ 'html' ][ 'href' ],
            ];
        })->toArray();
    }
}
