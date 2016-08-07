<?php

namespace Laradic\Git;

use Github\Client;
use Illuminate\Contracts\Foundation\Application;
use League\Flysystem\Filesystem;
use Laradic\Git\Remotes\Adapters\Bitbucket;
use Laradic\Git\Remotes\Adapters\Github;
use Laradic\Support\ServiceProvider;

/**
 * The main service provider
 *
 * @author        Laradic
 * @copyright     Copyright (c) 2015, Laradic
 * @license       https://tldrlegal.com/license/mit-license MIT
 * @package       Laradic\Git
 */
class GitServiceProvider extends ServiceProvider
{
    protected $dir = __DIR__;

    protected $configFiles = [ 'laradic.git' ];

    protected $singletons = [
        'laradic.git' => Manager::class,
    ];

    protected $aliases = [
        'laradic.git' => Contracts\Manager::class,
    ];

    public function boot()
    {
        $app = parent::boot();

        $app[ 'laradic.git' ]->extend('github', function () {

            return new Remotes\Remote(new Github());
        });
        $app[ 'laradic.git' ]->extend('bitbucket', function () {

            return new Remotes\Remote(new Bitbucket());
        });
    }


    public function register()
    {
        $app = parent::register();
        $this->registerBitbucketFS();
        $this->registerGithubFS();

        $app->singleton('laradic.git.remote', function (Application $app) {
            return $app[ 'laradic.git' ]->driver();
        });
    }


    protected function registerBitbucketFS()
    {
        $fsm = $this->app->make('filesystem');
        $fsm->extend('bitbucket', function (Application $app, $config) {


            $settings = new \Laradic\Git\Flysystem\Bitbucket\Settings($config[ 'repository' ], $config[ 'credentials' ], $config[ 'branch' ], $config[ 'reference' ]);
            $api      = new \Laradic\Git\Flysystem\Bitbucket\Api(new \Bitbucket\API\Api(), $settings);
            $adapter  = new \Laradic\Git\Flysystem\Bitbucket\BitbucketAdapter($api);

            return new Filesystem($adapter);
        });


        $fsm = $this->app->make('filesystem');
        $fsm->extend('bitbucket2', function (Application $app, $config) {
            $remote = $app->make('laradic.git')->connection($config[ 'connection' ]);

            $adapter = new \Laradic\Git\Flysystem\Bitbucket\BitbucketAdapter($remote, collect($config));

            return new Filesystem($adapter);
        });
    }

    protected function registerGithubFS()
    {
        $fsm = $this->app->make('filesystem');
        $fsm->extend('github', function (Application $app, $config) {

            $settings = new \Laradic\Git\Flysystem\Github\Settings($config[ 'repository' ], $config[ 'credentials' ], $config[ 'branch' ], $config[ 'reference' ]);
            $api      = new \Laradic\Git\Flysystem\Github\Api(new Client(), $settings);
            $adapter  = new \Laradic\Git\Flysystem\Github\GithubAdapter($api);

            return new Filesystem($adapter);
        });
    }
}
