<?php
/**
 * Part of the $author$ PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */


namespace Laradic\Git\Exceptions;


class CredentialTypeNotSupported extends \Exception
{
    public static function type($type)
    {
        return new static("Credential type [$type] not supported");
    }

    public function remote($remote)
    {
        $this->message .= "for remote [{$remote}]";
        return $this;
    }
}
