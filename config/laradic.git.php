<?php


return [
    'transformers' => [
        'github'    => Laradic\Git\Remotes\Transformers\GithubTransformer::class,
        'bitbucket' => Laradic\Git\Remotes\Transformers\BitbucketTransformer::class,
    ],
];
