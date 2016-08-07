<?php
/**
 * Part of the Docit PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Tests\Git;

use vierbergenlars\SemVer\version;

/**
 * This is the class EqualRemoteDataTest.
 *
 * @package        Laradic\Tests
 * @author         Docit
 * @copyright      Copyright (c) 2015, Docit. All rights reserved
 */
class EqualRemoteDataTest extends TestCase
{
    /**
     * @var \Laradic\Git\Remotes\Remote
     */
    protected $gh;

    /**
     * @var \Laradic\Git\Remotes\Remote
     */
    protected $bb;

    protected $repo = 'blade-extensions';

    protected function start()
    {
        $envPath = __DIR__ . '/../../../../';
        $envFile = $this->app->environmentFile();
        \Dotenv::load($envPath, $envFile);
        $this->registerServiceProvider();
        $git      = $this->app->make('laradic.git');
        $this->gh = $git->connection('github');
        $this->bb = $git->connection('bitbucket');
    }


    public function testGetUser()
    {
        $gh = $this->gh->getUser();
        $bb = $this->bb->getUser();
        $this->assertEquals(array_keys($gh), array_keys($bb));
    }



    public function testGetBranches()
    {
        $gh = $this->gh->getBranches($this->repo);
        $bb = $this->bb->getBranches($this->repo);
        foreach ($gh as $branch => $sha) {
            $this->assertInArray($branch, array_keys($bb));
        }
    }

    public function testGetBranch()
    {
        $gh = $this->gh->getBranch($this->repo, 'develop');
        $bb = $this->bb->getBranch($this->repo, 'develop');
        $this->assertEquals(array_keys($gh), array_keys($bb));
    }

    public function testGetTags()
    {
        $gh = $this->gh->getTags($this->repo);
        $bb = $this->bb->getTags($this->repo);

    }
}
