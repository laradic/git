<?php
/**
 * Part of the $author$ PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */


namespace Laradic\Git\Filesystem;


use Illuminate\Contracts\Filesystem\Cloud as CloudFilesystemContract;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Laradic\Git\Remotes\AdapterInterface;
use Laradic\Git\Remotes\Remote;

abstract class AbstractFilesystem implements FilesystemContract, CloudFilesystemContract
{
    protected $api;

    protected $repo;

    protected $owner;

    protected $ref;

    protected $config;

    /**
     * AbstractFilesystem constructor.
     *
     * @param \Laradic\Git\Remotes\Remote $remote
     * @param array                       $config
     */
    public function __construct(Remote $remote, array $config = [])
    {
        $this->config = collect($config);
        $this->api = $remote->connect($config);
    }

    protected function repo(){
        return $this->config->get('repository');
    }
    protected function owner(){
        return $this->config->get('owner');
    }
    protected function ref(){
        return $this->config->get('ref', 'master');
    }


    /**
     * Determine if a file exists.
     *
     * @param  string $path
     *
     * @return bool
     */
    public function exists($path)
    {
        $this->api->getRaw($this->repo(), $this->ref(), $path, $this->owner());
    }

    /**
     * Get the contents of a file.
     *
     * @param  string $path
     *
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get($path)
    {
        // TODO: Implement get() method.
    }

    /**
     * Write the contents of a file.
     *
     * @param  string          $path
     * @param  string|resource $contents
     * @param  string          $visibility
     *
     * @return bool
     */
    public function put($path, $contents, $visibility = null)
    {
        // TODO: Implement put() method.
    }

    /**
     * Get the visibility for the given path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function getVisibility($path)
    {
        // TODO: Implement getVisibility() method.
    }

    /**
     * Set the visibility for the given path.
     *
     * @param  string $path
     * @param  string $visibility
     *
     * @return void
     */
    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * Prepend to a file.
     *
     * @param  string $path
     * @param  string $data
     *
     * @return int
     */
    public function prepend($path, $data)
    {
        // TODO: Implement prepend() method.
    }

    /**
     * Append to a file.
     *
     * @param  string $path
     * @param  string $data
     *
     * @return int
     */
    public function append($path, $data)
    {
        // TODO: Implement append() method.
    }

    /**
     * Delete the file at a given path.
     *
     * @param  string|array $paths
     *
     * @return bool
     */
    public function delete($paths)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Copy a file to a new location.
     *
     * @param  string $from
     * @param  string $to
     *
     * @return bool
     */
    public function copy($from, $to)
    {
        // TODO: Implement copy() method.
    }

    /**
     * Move a file to a new location.
     *
     * @param  string $from
     * @param  string $to
     *
     * @return bool
     */
    public function move($from, $to)
    {
        // TODO: Implement move() method.
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string $path
     *
     * @return int
     */
    public function size($path)
    {
        // TODO: Implement size() method.
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string $path
     *
     * @return int
     */
    public function lastModified($path)
    {
        // TODO: Implement lastModified() method.
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param  string|null $directory
     * @param  bool        $recursive
     *
     * @return array
     */
    public function files($directory = null, $recursive = false)
    {
        // TODO: Implement files() method.
    }

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param  string|null $directory
     *
     * @return array
     */
    public function allFiles($directory = null)
    {
        // TODO: Implement allFiles() method.
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string|null $directory
     * @param  bool        $recursive
     *
     * @return array
     */
    public function directories($directory = null, $recursive = false)
    {
        // TODO: Implement directories() method.
    }

    /**
     * Get all (recursive) of the directories within a given directory.
     *
     * @param  string|null $directory
     *
     * @return array
     */
    public function allDirectories($directory = null)
    {
        // TODO: Implement allDirectories() method.
    }

    /**
     * Create a directory.
     *
     * @param  string $path
     *
     * @return bool
     */
    public function makeDirectory($path)
    {
        // TODO: Implement makeDirectory() method.
    }

    /**
     * Recursively delete a directory.
     *
     * @param  string $directory
     *
     * @return bool
     */
    public function deleteDirectory($directory)
    {
        // TODO: Implement deleteDirectory() method.
}}
