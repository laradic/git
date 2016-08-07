<?php

namespace Laradic\Git;

use Closure;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Laradic\Git\Contracts\Manager as ManagerContract;

class Manager implements ManagerContract
{
    const AUTH_BASIC = 'basic';
    const AUTH_TOKEN = 'token';
    const AUTH_OAUTH = 'oauth';
    const AUTH_OAUTH2 = 'oauth2';

    public function getCredentialTypes()
    {
        return [
            static::AUTH_BASIC,
            static::AUTH_TOKEN,
            static::AUTH_OAUTH,
            static::AUTH_OAUTH2,
        ];
    }

    /** @var \Illuminate\Contracts\Container\Container */
    protected $app;

    /** @var array */
    protected $connections = [ ];

    /** @var array */
    protected $connectors = [ ];

    /**
     * Manager constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }


    /**
     * connected method
     *
     * @param null $name
     *
     * @return bool
     */
    public function connected($name)
    {
        return isset($this->connections[ $name ]);
    }

    /**
     * connection method
     *
     * @param string $name
     *
     * @return \Laradic\Git\Remotes\Remote
     */
    public function connection($name)
    {
        if ( !isset($this->connections[ $name ]) ) {
            $this->connections[ $name ] = $this->resolve($name);
        }

        return $this->connections[ $name ];
    }

    public function disconnect($name)
    {
        if ( isset($this->connections[ $name ]) ) {
            $this->connections[ $name ] = null;
        }
    }

    /**
     * Resolves given the connection
     *
     * @param $name
     *
     * @return mixed
     */
    protected function resolve($name)
    {
        $config      = $this->getConfig($name);
        $transformer = $this->getTransformer($config[ 'driver' ]);

        return $this->getConnector($config[ 'driver' ])
            ->setTransformer($transformer)
            ->connect($config);
    }


    /**
     * getConnector method
     *
     * @param $driver
     *
     * @return Remotes\Remote
     */
    protected function getConnector($driver)
    {
        if ( isset($this->connectors[ $driver ]) ) {
            return call_user_func($this->connectors[ $driver ]);
        }

        throw new InvalidArgumentException("No connector for [$driver]");
    }


    /**
     * extend method
     *
     * @param          $driver
     * @param \Closure $resolver
     */
    public function extend($driver, Closure $resolver)
    {
        $this->connectors[ $driver ] = $resolver;
    }

    /**
     * getConfig method
     *
     * @param $name
     *
     * @return mixed
     */
    public function getConfig($name)
    {
        return $this->app[ 'config' ][ "services.{$name}" ];
    }

    /**
     * getDefaultDriver method
     *
     * @return mixed
     */
    public function getTransformer($driver)
    {
        return $this->app[ 'config' ][ "laradic.git.transformers.{$driver}" ];
    }

    public function __call($method, $parameters)
    {
        $callable = [ head($this->connections), $method ];

        return call_user_func_array($callable, $parameters);
    }
}
