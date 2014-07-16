**This code is part of the [SymfonyContrib](http://symfonycontrib.com/) community.**

# Symfony2 CronBundle

###Features:

* Create and manage repeatable application tasks.
* Tasks can be any method on a publicly accessible Symfony service.
* Symfony command to execute tasks.

## Installation

Installation is similar to a standard bundle.
http://symfony.com/doc/current/cookbook/bundles/installation.html

* Add bundle to composer.json: https://packagist.org/packages/symfonycontrib/cron-bundle
* Add bundle to AppKernel.php:

```php
new SymfonyContrib\Bundle\CronBundle\CronBundle(),
```

## Usage Examples

**Administration of database entry is not yet complete so entries need to be
added manually.**

See entity for info on db entry needed.

Example job: cron.executer:helloWorld
