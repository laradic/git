<?php
namespace Laradic\Git\Exceptions;

use RuntimeException;

class LaradicGitException extends RuntimeException
{
    public static function credentialTypeNotSupported($msg = '')
    {
        return new static('[Credential Type Not Supported] ' . $msg);
    }
}
