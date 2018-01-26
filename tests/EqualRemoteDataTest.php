<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2018. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2018 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
 */

namespace Laradic\Tests\Git;

use Dotenv\Dotenv;


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
        $env     = new Dotenv($envPath, $envFile);
        $env->load();
        app()->make('config')->set('services.bitbucket', [
            'driver'   => 'bitbucket', // bitbucket | github
            'auth'     => \Laradic\Git\Manager::AUTH_BASIC,
            'username' => env('BITBUCKET_USERNAME'),
            'password' => env('BITBUCKET_USERNAME'),
        ]);
        app()->make('config')->set('services.github', [
            'driver' => 'github', // bitbucket | github
            'auth'   => \Laradic\Git\Manager::AUTH_TOKEN,
            'secret' => env('GITHUB_TOKEN'),
        ]);
        $this->registerServiceProvider();
        /** @var \Laradic\Git\Manager $git */
        $git = $this->app->make('laradic.git');

        $this->gh = $git->connection('github');
        $this->bb = $git->connection('bitbucket');
    }


    public function testGetUser()
    {
        $gh = $this->gh->getUser();
        $bb = $this->bb->getUser();
        $this->assertThat($gh, self::isType('array'));
        $this->assertThat($bb, self::isType('array'));
        $this->assertEquals(array_keys($gh), array_keys($bb));
    }


    public function testGetBranches()
    {
        $gh = $this->gh->getBranches($this->repo);
        $bb = $this->bb->getBranches($this->repo);
        $this->assertThat($gh, self::isType('array'));
        $this->assertThat($bb, self::isType('array'));
        foreach ($gh as $branch => $sha) {
            $this->assertInArray($branch, array_keys($bb));
        }
    }

    public function testGetBranch()
    {
        $gh = $this->gh->getBranch($this->repo, 'develop');
        $bb = $this->bb->getBranch($this->repo, 'develop');
        $this->assertThat($gh, self::isType('array'));
        $this->assertThat($bb, self::isType('array'));
        $this->assertEquals(array_keys($gh), array_keys($bb));
    }

    public function testGetTags()
    {
        $gh = $this->gh->getTags($this->repo);
        $bb = $this->bb->getTags($this->repo);
        $this->assertThat($gh, self::isType('array'));
        $this->assertThat($bb, self::isType('array'));
        $this->assertEquals(array_keys($gh), array_keys($bb));

    }
}
