<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Console;

/**
 * This is the class GitConsoleTrait.
 *
 * @package        Laradic\Git
 * @author         Laradic
 * @copyright      Copyright (c) 2015, Laradic. All rights reserved
 * @mixin \Illuminate\Console\Command
 */
trait GitConsoleTrait
{
    protected $remote;

    /**
     * getRemote method
     *
     * @param null $name
     * @return \Laradic\Git\Remotes\Remote
     */
    protected function getRemote($name = null)
    {
        return app('laradic.git')->connection($name === null ? $this->remote : $name);
    }


    protected function chooseRemote()
    {
        return $this->remote = $this->choice('Choose remote', [ 'github', 'bitbucket' ]);
    }

    /**
     * chooseOrganisation
     *
     * @param \Github\Client $github
     * @return string
     */
    protected function chooseOrganisation($includeUsername = true)
    {
        $orgs = $this->getRemote()->listOrganisations();
        if ($includeUsername) {
            $orgs[] = $this->getRemote()->getUsername();
        }
        return $this->choice('Choose organisation', $orgs);
    }

    /**
     * chooseRepository
     *
     * @param \Github\Client $github
     * @param string|null    $org
     * @return string
     */
    protected function chooseRepository($org)
    {
        return $this->choice('Choose repository', $this->getRemote()->listRepositories($org === $this->getRemote()->getUsername() ? null : $org));
    }

    /**
     * chooseBranch
     *
     * @param \Github\Client $github
     * @param                $org
     * @param                $repo
     * @return bool
     */
    protected function chooseBranch($org, $repo)
    {
        return $this->choice('Choose branch', $this->getRemote()->getBranches($repo, $org === $this->getRemote()->getUsername() ? null : $org));
    }
}
