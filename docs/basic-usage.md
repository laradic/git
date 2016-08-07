<!---
title: Basic usage
-->

#### Local git
Besides providing a common interface for the REST API's, `laradic/git` also has **local** git repository functionality.
Most of this (if not all) is provided by the `gitonomy/git` library. As such, for more documentation about local git
manipulation, you should visit their documentation.

```php
# returns an instance of `Gitonomy\Git\Repository`
$local = Git::local($path, $config = [ ]); 
```

#### Behind the scene
The `Git` facade points to the `laradic.git` singleton binding in the IoC container which is an instance of `Laradic\Git\Manager`.
You can either use the facade or use Constructor Dependency Injection like:

```php
public function __construct(\Laradic\Git\Contracts\Manager $manager){
    $this->manager = $manager;
    
    # Returns an instance of `\Laradic\Git\Remotes\Remote`.
    $remote = $manager->connection('bitbucket'); 
}
```

The `Laradic\Git\Remotes\Remote` class uses PHP's magic method `__call` to call methods on either 
the `Laradic\Git\Remotes\Adapters\Bitbucket` or the `Laradic\Git\Remotes\Adapters\Github` class.
 
Before passing back the result, it will call the `Transformer` that will re-structure/re-name the output.
It is possible to use your own `Transformer` if you wish to alter the return data.

#### The API functions
```php
Git::getUser();
Git::getUsername();
Git::listWebhooks($repo, $owner = null);
Git::getWebhook($repo, $uuid, $owner = null);
Git::createWebhook($repo, array $data, $owner = null);
Git::removeWebhook($repo, $uuid, $owner = null);
Git::listOrganisations($owner = null);
Git::listRepositories($owner = null);
Git::createRepository($repo, array $data = [ ], $owner = null);
Git::deleteRepository($repo, $owner = null);
Git::getBranches($repo, $owner = null);
Git::getMainBranch($repo, $owner = null);
Git::getRepositoryManifest($repo, $ref, $owner = null);
Git::getTags($repo, $owner = null);
Git::getRaw($repo, $ref, $path, $owner = null);
Git::getChangeset($repo, $ref, $path, $owner = null);
Git::getRepositoryCommits($repo, $owner = null);
Git::getBranchCommits($repo, $branch, $owner = null);
```

#### Filesystem 
