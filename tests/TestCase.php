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


abstract class TestCase extends \Laradic\Testing\Laravel\AbstractTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getServiceProviderClass()
    {
        return \Laradic\Git\GitServiceProvider::class;
    }

    protected function getPackageRootPath()
    {
        return __DIR__ . '/..';
    }
}
