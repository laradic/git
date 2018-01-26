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

use Laradic\Testing\Laravel\Traits\ServiceProviderTester;

class GitServiceProviderTest extends TestCase
{
    use ServiceProviderTester;

    public function testOne()
    {
        $this->assertTrue(true);

    }
}
