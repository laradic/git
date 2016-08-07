<!---
title: Installation
-->

#### Installation
The `laradic/git` provides a common interface to communiate to both `bitbucket` and `github`  REST API's.

- Add the `laradic/git` package to your composer.json dependencies.
```json
"require": {
    "laradic/git": "1.0.*"
}
```

- Register the `GitServiceProvider` in your application, preferably in your `config/app.php` file.
```php
'providers' => [
    Laradic\Git\GitServiceProvider::class
]
```

- Optional: Add the facade
```php
'facades' => [
    'Asset' => Laradic\Git\Facades\Git::class
]
```

- Optional: Publish vendor files
```sh
php artisan vendor:publish --provider=Laradic\Git\GitServiceProvider
```


#### Configuration
The configuration file pretty much speaks for itself. 
You'll want to create a [Github](#) oauth2 token and a [Bitbucket](#) webhook. 
It's recommended you add those settings into your `.env` file like so:
```sh
BITBUCKET_CLIENT_KEY=xxxx
BITBUCKET_CLIENT_SECRET=xxxx
GITHUB_TOKEN=xxxx
```
