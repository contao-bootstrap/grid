Contao-Bootstrap Grid Component
===============================


[![Version](http://img.shields.io/packagist/v/contao-bootstrap/grid.svg?style=for-the-badge&label=Latest)](http://packagist.org/packages/contao-bootstrap/grid)
[![GitHub issues](https://img.shields.io/github/issues/contao-bootstrap/grid.svg?style=for-the-badge&logo=github)](https://github.com/contao-bootstrap/grid/issues)
[![License](http://img.shields.io/packagist/l/contao-bootstrap/grid.svg?style=for-the-badge&label=License)](http://packagist.org/packages/contao-bootstrap/grid)
[![Build Status](https://img.shields.io/github/workflow/status/contao-bootstrap/grid/contao-bootstra-grid?logo=githubactions&logoColor=%23fff&style=for-the-badge)](https://github.com/contao-bootstrap/grid/actions)
[![Downloads](http://img.shields.io/packagist/dt/contao-bootstrap/grid.svg?style=for-the-badge&label=Downloads)](http://packagist.org/packages/contao-bootstrap/grid)

This extension provides Bootstrap 5 grid tools for Contao CMS.

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

- PHP ^8.1
- Contao ^4.13 || ^5.3

Install
-------

### Managed edition

When using the managed edition it's pretty simple to install the package. Just search for the package in the
Contao Manager and install it. Alternatively you can use the CLI.

```bash
# Using the contao manager
$ php contao-manager.phar.php composer require contao-bootstrap/grid ^3.0

# Using composer directly
$ php composer.phar require contao-bootstrap/grid ^3.0
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
            new \ContaoBootstrap\Core\ContaoBootstrapCoreBundle(),
            new \ContaoCommunityAlliance\MetaPalettes\CcaMetaPalettesBundle(),
            new \Netzmacht\Contao\Toolkit\Bundle\NetzmachtContaoToolkitBundle(),
            new \Mvo\ContaoGroupWidget\MvoContaoGroupWidgetBundle(),
            new \ContaoBootstrap\Core\ContaoBootstrapCoreBundle(),
            new \ContaoBootstrap\Grid\ContaoBootstrapGridBundle()
        ];
    }
}

```

Migration
---------

To automatically migrate your grid from Start- and Stop-Wrappers to nested fragments, you have to enable the migration
via the bundle configuration. Create or extend the file `config/packages/contao_bootstrap_grid.yaml` in your Symfony
application:

```yaml
contao_bootstrap_grid:
    enable_wrapper_migration: true
```

Afterwards you can run the migration in the Contao Manager or via CLI:

```bash
$ php vendor/bin/contao-console contao:migrate
```

Deprecated
----------

The legacy content elements `bs_gridStart`, `bs_gridStop` and `bs_gridSeparator` are deprecated and will be removed in
a future major version. Use `bs_grid_wrapper` instead.

To disable the legacy elements now, set the following configuration:

```yaml
contao_bootstrap_grid:
    enable_legacy_elements: false
```
