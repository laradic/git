Laradic Git
====================


[![Build Status](https://img.shields.io/travis/laradic/git.svg?&style=flat-square)](https://travis-ci.org/laradic/git)
[![Scrutinizer coverage](https://img.shields.io/scrutinizer/coverage/g/laradic/git.svg?&style=flat-square)](https://scrutinizer-ci.com/g/laradic/git)
[![Scrutinizer quality](https://img.shields.io/scrutinizer/g/laradic/git.svg?&style=flat-square)](https://scrutinizer-ci.com/g/laradic/git)
[![Source](http://img.shields.io/badge/source-laradic/git-blue.svg?style=flat-square)](https://github.com/laradic/git)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

The `laradic/git` package provides seemless switching between `github` and `bitbucket` API calls and filesystem operations. 
Created for the **Laravel 5** framework.

The package follows the FIG standards PSR-1, PSR-2, and PSR-4 to ensure a high level of interoperability between shared PHP code.

Quick Overview
-------------
For the **full documenation**, check out the [laradic-git](/laradic-git) package documenatation.

#### Configuration
Define your connections in `config/services.php`.
```php
return [
    'conn1' => [
        'driver'    => 'bitbucket', // bitbucket | github
        'auth'      => Laradic\Git\Manager::AUTH_BASIC,
        'username'  => 'user1',
        'password'  => 'passwd'                
    ],
    'conn2' => [
        'driver'    => 'bitbucket', // bitbucket | github
        'auth'      => Laradic\Git\Manager::AUTH_OAUTH,
        'key'       => 'a#W23r2baaaf',
        'secret'    => 'we8r9w1ef32f'                
    ],
    'conn3' => [
        'driver'    => 'github', // bitbucket | github
        'auth'      => Laradic\Git\Manager::AUTH_TOKEN,
        'secret'    => 'asAER4562aw32po'       
    ]
];    
```


#### Filesystem
```php
$fs = Git::getFilesystem($repo, $owner = null, $ref = null);
$fs->exists('composer.json');
$com = $fs->get('composer.json');
// the other Flysystem methods yo are most likely familiar with..
```

#### API calls
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

#### Transformers
Transformers are responsible for transforming the raw api call response data into a common, similar response.
If wanted, you can extend and use your own implementation. Also provided is a `NullTransformer` which won't transform anything.

```php
Git::setTransformer(NullTransformer::class);
Git::setTransformer(BitbucketTransformer::class);
Git::setTransformer(GithubTransformer::class);
```
