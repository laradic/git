<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Remotes;

use Laradic\Git\Remotes\Adapters\AbstractAdapter;

/**
 * This is the class RemoteInterface.
 *
 * @author    Laradic
 * @copyright Copyright (c) 2015, Laradic. All rights reserved
 */
interface AdapterInterface
{
    /**
     * name method
     *
     * @return string
     */
    public function name();

    public function connect($config);

    /**
     * getUser method
     *
     * @return array
     */
    public function getUser();

    /**
     * getUsername method
     *
     * @return string
     */
    public function getUsername();

    public function listWebhooks($repo, $owner = null);

    public function getWebhook($repo, $uuid, $owner = null);

    public function createWebhook($repo, array $data, $owner = null);

    public function removeWebhook($repo, $uuid, $owner = null);

    public function listOrganisations($owner = null);

    public function listRepositories($owner = null);

    public function createRepository($repo, array $data = [ ], $owner = null);

    public function deleteRepository($repo, $owner = null);

    public function getBranch($repo, $ref, $owner = null);

    public function getBranches($repo, $owner = null);

    public function getMainBranch($repo, $owner = null);

    public function getRepositoryManifest($repo, $ref, $owner = null);

    public function getTags($repo, $owner = null);

    public function getRaw($repo, $ref, $path, $owner = null);

    public function getChangeset($repo, $ref, $path, $owner = null);

    public function getRepositoryCommits($repo, $owner = null);

    public function getBranchCommits($repo, $branch, $owner = null);

    public function getContentList($path, $repo, $ref, $owner = null);

    // todo implement https://developer.github.com/v3/repos/contents/#get-archive-link
    public function downloadArchive($repo, $ref, $localPath, $owner = null);}
