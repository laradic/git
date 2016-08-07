<?php
/**
 * Part of the Laradic PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Git\Remotes;

/**
 * This is the class TransformerInterface.
 *
 * @package        Laradic\Git
 * @author         Laradic
 * @copyright      Copyright () 2015, Laradic. All rights reserved
 */
interface TransformerInterface
{
    public function transform($data, $functionName);
}
