<?php

namespace Laradic\Tests\Git;

use Laradic\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
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
