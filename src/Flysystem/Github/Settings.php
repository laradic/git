<?php
namespace Laradic\Git\Flysystem\Github;

use Github\Client;
use Laradic\Git\Flysystem\SettingsInterface;

/**
 * This is the class Settings.
 *
 * @package        Laradic\Git
 * @author         Potherca
 * @copyright      Copyright (c) 2015 Potherca <potherca@gmail.com>
 */
class Settings implements SettingsInterface
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\
    const AUTHENTICATE_USING_TOKEN = Client::AUTH_URL_TOKEN;
    const AUTHENTICATE_USING_PASSWORD = Client::AUTH_HTTP_PASSWORD;

    const BRANCH_MASTER = 'master';
    const REFERENCE_HEAD = 'HEAD';

    const ERROR_INVALID_REPOSITORY_NAME = 'Given Repository name "%s" should be in the format of "vendor/project"';

    /** @var string */
    private $branch;
    /** @var array */
    private $credentials;
    /** @var string */
    private $reference;
    /** @var string */
    private $repository;
    /** @var string */
    private $vendor;
    /** @var string */
    private $package;

    //////////////////////////// SETTERS AND GETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return string
     */
    final public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @return array
     */
    final public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @return string
     */
    final public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return string
     */
    final public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    final public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    final public function getVendor()
    {
        return $this->vendor;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    final public function __construct(
        $repository,
        array $credentials = [],
        $branch = self::BRANCH_MASTER,
        $reference = self::REFERENCE_HEAD
    ) {
        $this->isValidRepositoryName($repository);

        $this->branch = (string) $branch;
        $this->credentials = $credentials;
        $this->reference = (string) $reference;
        $this->repository = (string) $repository;

        list($this->vendor, $this->package) = explode('/', $repository);
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @param $repository
     */
    private function isValidRepositoryName($repository)
    {
        if (is_string($repository) === false
            || strpos($repository, '/') === false
            || strpos($repository, '/') === 0
            || substr_count($repository, '/') !== 1
        ) {
            $message = sprintf(
                self::ERROR_INVALID_REPOSITORY_NAME,
                var_export($repository, true)
            );
            throw new \InvalidArgumentException($message);
        }
    }
}
