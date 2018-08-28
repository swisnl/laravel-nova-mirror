# Automated Laravel Nova Mirror

[![PHP from Packagist](https://img.shields.io/packagist/php-v/swisnl/laravel-nova-mirror.svg)](https://packagist.org/packages/swisnl/laravel-nova-mirror)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/swisnl/laravel-nova-mirror.svg)](https://packagist.org/packages/swisnl/laravel-nova-mirror)


This application enables you to update a private mirror of Laravel Nova automatically. It downloads releases from the Nova site and updates a repository with the correct releases. 

The application assumes the user which runs it has git access to the repository. The best way to do this using an ssh key. You need to enter the username and password to your account on nova.laravel.org to get this to work.

This repository was created because Nova only supplies a download which you need to copy to your project. This feels way too old-school and makes updating a bit hard. Using this application you can include nova directly from the git repository which should make updating a lot easier.

Run this on a CI once a day, and you will have an up-to-date Nova available whenever you need it.   

Please note; the repository you mirror to must be private, as per license agreement with Nova. 

## Getting started

1. Start with installing the project with composer ```composer create-project swis/laravel-nova-mirror```
1. Setup the .env file (or environment) with the correct credentials. 
1. Make sure the remote repository exists.
1. Run `php artisan nova-mirror:update` to download and push the repository or run Dusk directly `php artisan dusk` to do the same.
1. Setup your Nova project to pull from your private repository.

```json
"require" : {
    "laravel/framework": "5.6.*",
    "laravel/nova": "^1"
},
"repositories": [
    {
        "type": "vcs",
        "url": "git@bitbucket.org:username/laravel-nova.git"
    }
],
```



## Security

If you discover any security related issues, please email security@swis.nl instead of using the issue tracker.

## License

The MIT License (MIT). 

## SWIS

[SWIS](https://www.swis.nl) is a web agency from Leiden, the Netherlands. We love working with open source software.
