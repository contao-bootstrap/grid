Contao-Bootstrap Grid Component
===============================

[![Build Status](http://img.shields.io/travis/contao-bootstrap/grid/master.svg?style=flat-square)](https://travis-ci.org/contao-bootstrap/grid)
[![Version](http://img.shields.io/packagist/v/contao-bootstrap/grid.svg?style=flat-square)](http://packagist.org/packages/contao-bootstrap/grid)
[![License](http://img.shields.io/packagist/l/contao-bootstrap/grid.svg?style=flat-square)](http://packagist.org/packages/contao-bootstrap/grid)
[![Downloads](http://img.shields.io/packagist/dt/contao-bootstrap/grid.svg?style=flat-square)](http://packagist.org/packages/contao-bootstrap/grid)
[![Contao Community Alliance coding standard](http://img.shields.io/badge/cca-coding_standard-red.svg?style=flat-square)](https://github.com/contao-community-alliance/coding-standard)


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

### Standard edition

Without the contao manager you also have to register the bundle

```php

class AppKernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Contao\CoreBundle\HttpKernel\Bundle\ContaoModuleBundle('metapalettes', $this->getRootDir()),
            new Contao\CoreBundle\HttpKernel\Bundle\ContaoModuleBundle('multicolumnwizard', $this->getRootDir()),
            new ContaoBootstrap\Core\ContaoBootstrapCoreBundle(),
            new ContaoBootstrap\Grid\ContaoBootstrapGridBundle()
        ];
    }
}

```
