Contao-Bootstrap Grid Component
===============================


[![Version](http://img.shields.io/packagist/v/contao-bootstrap/grid.svg?style=for-the-badge&label=Latest)](http://packagist.org/packages/contao-bootstrap/grid)
[![GitHub issues](https://img.shields.io/github/issues/contao-bootstrap/grid.svg?style=for-the-badge&logo=github)](https://github.com/contao-bootstrap/grid/issues)
[![License](http://img.shields.io/packagist/l/contao-bootstrap/grid.svg?style=for-the-badge&label=License)](http://packagist.org/packages/contao-bootstrap/grid)
[![Build Status](http://img.shields.io/travis/contao-bootstrap/grid/master.svg?style=for-the-badge&logo=travis)](https://travis-ci.org/contao-bootstrap/grid)
[![Downloads](http://img.shields.io/packagist/dt/contao-bootstrap/grid.svg?style=for-the-badge&label=Downloads)](http://packagist.org/packages/contao-bootstrap/grid)

This extension provides Bootstrap 4 grid tools for Contao CMS.

Features
--------

 - Manage grid definition in your theme settings
 - Content elements
 - Form elements
 - Grid module 
 - Import/Export with your theme settings
 
Changelog
---------

See [changelog](CHANGELOG.md)
 
Requirements
------------

 - PHP 7.1
 - Contao ~4.4
 
 
Install
-------

### Managed edition

When using the managed edition it's pretty simple to install the package. Just search for the package in the
Contao Manager and install it. Alternatively you can use the CLI.  

```bash
# Using the contao manager
$ php contao-manager.phar.php composer require contao-bootstrap/grid~2.0@beta

# Using composer directly
$ php composer.phar require contao-bootstrap/grid~2.0@beta
```

### Symfony application

If you use Contao in a symfony application without contao/manager-bundle, you have to register following bundles 
manually:

```php

class AppKernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Contao\CoreBundle\HttpKernel\Bundle\ContaoModuleBundle('metapalettes', $this->getRootDir()),
            new Contao\CoreBundle\HttpKernel\Bundle\ContaoModuleBundle('multicolumnwizard', $this->getRootDir()),
            new Netzmacht\Contao\Toolkit\Bundle\NetzmachtContaoToolkitBundle(),
            new ContaoBootstrap\Core\ContaoBootstrapCoreBundle(),
            new ContaoBootstrap\Grid\ContaoBootstrapGridBundle()
        ];
    }
}

```
