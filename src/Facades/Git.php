<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the class Git.
 *
 * @author    Laradic
 * @copyright Copyright (c) 2015, Laradic. All rights reserved
 */
class Git extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laradic.git';
    }
}
