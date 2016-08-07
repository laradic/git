<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Remotes\Adapters;

/**
 * This is the class AbstractRemote.
 *
 * @author    Laradic
 * @copyright Copyright (c) 2015, Laradic. All rights reserved
 */
abstract class AbstractAdapter
{
    const DRIVER = '';

    /** Instantiates the class
     *
     * @param \Laradic\Git\Remotes\TransformerInterface $transformer
     * @param array                                     $credentials
     */
    public function __construct()
    {
    }
    public function name()
    {
        return static::DRIVER;
    }

    protected function owner(&$owner)
    {
        return $owner = is_null($owner) ? $this->getUsername() : $owner;
    }

    abstract public function getUsername();
}
