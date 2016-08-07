<!---
title: Flysystem
-->

#### Flysystem adapters
Flysystem is a filesystem abstraction which allows you to easily swap out a local filesystem for a remote one.
It is created and maintained by the PHP League. For more information about Flysystem, check out their [documentation](http://flysystem.thephpleague.com) 

Laravel uses the Flysystem, and `laradic/git` provides adapters for both `Bitbucket` and `Github`.

To get a instance of the Flysystem, use the `getFilesystem($repo, $owner = null, $ref = null)` method on the `Laradic\Git\Remotes\Remote` instance.


```php
$fs->__construct(\Laradic\Git\Contracts\Manager $manager){
    $this->manager = $manager;
    
    # Returns an instance of `\Laradic\Git\Remotes\Remote`.
    $remote = $manager->connection('bitbucket'); 
    $fs = $remote->getFilesystem('reponame', 'owner', 'master')
    $fs->exists('path');
    $fs->get('path');
}
```

#### Available methods
The flysystem implements the `Illuminate\Contracts\Filesystem\Filesystem` interface, which allows usage of the following methods:

```php
$fs->exists($path);
$fs->get($path);
$fs->put($path, $contents, $visibility = null);
$fs->getVisibility($path);
$fs->setVisibility($path, $visibility);
$fs->prepend($path, $data);
$fs->append($path, $data);
$fs->delete($paths);
$fs->copy($from, $to);
$fs->move($from, $to);
$fs->size($path);
$fs->lastModified($path);
$fs->files($directory = null, $recursive = false);
$fs->allFiles($directory = null);
$fs->directories($directory = null, $recursive = false);
$fs->allDirectories($directory = null);
$fs->makeDirectory($path);
$fs->deleteDirectory($directory);
```
