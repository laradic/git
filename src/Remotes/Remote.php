<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Remotes;

use Laradic\Git\Flysystem\Bitbucket\Settings;
use Laradic\Git\Remotes\Transformers\NullTransformer;

/**
 * This is the class RemoiteAdapter.
 *
 * @package        Laradic\Git
 * @author         Laradic
 * @copyright      Copyright (c) 2015, Laradic. All rights reserved
 * @mixin          AdapterInterface
 */
class Remote
{
    protected $adapter;

    protected $transformer;

    protected $manager;

    protected $config;

    /** Instantiates the class
     *
     * @param \Laradic\Git\Contracts\Manager|\Laradic\Git\Factory $git
     * @param \Laradic\Git\Remotes\AdapterInterface               $adapter
     * @param \Laradic\Git\Remotes\TransformerInterface           $transformer
     */
    public function __construct(AdapterInterface $adapter, TransformerInterface $transformer = null)
    {
        $this->adapter     = $adapter;
        $this->transformer = isset($transformer) ? $transformer : new NullTransformer;
    }

    public function __call($name, $arguments)
    {
        if ( method_exists($this->adapter, $name) ) {
            $out = call_user_func_array([ $this->adapter, $name ], $arguments);

            return $this->transformer->transform($out, $name);
        }
    }

    /**
     * connect method
     *
     * @param $config
     *
     * @return $this
     */
    public function connect($config)
    {
        $this->config = $config;

        $this->adapter->connect($this->config);

        return $this;
    }

    /**
     * getFilesystem method
     *
     * @param        $repository
     * @param null   $owner
     * @param string $branch
     * @param string $reference
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getFilesystem($repository, $owner = null, $branch = Settings::BRANCH_MASTER, $reference = Settings::REFERENCE_HEAD)
    {
        $owner = $owner ?: $this->adapter->getUsername();
        $repository = $owner . '/' . $repository;
        $credentials = $this->config;

        $config = compact('repository', 'owner', 'branch', 'reference', 'credentials');
        $config['driver'] = $this->config[ 'driver' ];

        $ckey   = uniqid($this->config[ 'driver' ], false);
        app('config')->set('filesystems.disks.' . $ckey, $config);
        return app('filesystem')->drive($ckey);
    }

    /**
     * Set the transformer value
     *
     * @param \Laradic\Git\Remotes\Adapters\NullTransformer|\Laradic\Git\Remotes\TransformerInterface $transformer
     *
     * @return Remote
     */
    public function setTransformer($transformer)
    {
        if ( !$transformer instanceof TransformerInterface ) {
            $transformer = new $transformer;
        }
        $this->transformer = $transformer;

        return $this;
    }

    public function disableTransformer()
    {
        $this->transformer = new NullTransformer;
    }
}
