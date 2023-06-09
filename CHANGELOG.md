
Changelog
=========

[Unreleased]
------------

[3.0.0] (2023-06-09)

### Changed

 - Add xxl breakpoint for Bootstrap 5 compatibility
 - Rename `no-gutter` to `g-0` for Bootstrap 5 compatibility

[2.4.3] (2022-08-18)

### Fixed

 - Fix undefined array index ([#52](https://github.com/contao-bootstrap/grid/pull/52))
 - Remove superfluous space in separator template ([#53](https://github.com/contao-bootstrap/grid/pull/53))

[2.4.2] (2022-08-18)

### Fixed

 - Fixed type error when auto generating grid elements for forms

[2.4.1] (2022-06-01)
--------------------

### Fixed

 - Fix dependency of symfony/dependency-container ([#49](https://github.com/contao-bootstrap/grid/pull/51), [#49](https://github.com/contao-bootstrap/grid/issues/50))


[2.4.0] (2022-04-20)
--------------------

### Changed

 - Bump minimum PHP version to 7.4
 - Bump Symfony requirements to ^4.4 or ^5.4
 - Bump Contao requirements to ^4.9 or ^4.13
 - Changed coding standard
 - Rewrite modules and elements to fragment controller

### Fixed

 - Prevent migration if table does not exist ([#49](https://github.com/contao-bootstrap/grid/pull/49))
 - Prevent issues with grid size settings ([#48](https://github.com/contao-bootstrap/grid/issues/48))


[2.3.1] (2020-11-16)
--------------------

### Fixed

 - Prevent sql error if tables `tl_theme` or `tl_bs_grid` does not exist
 - Prevent security issue occurred in `symfony/http-kernel`


[2.3.0] (2020-08-28)
--------------------

### Added

 - Add ability to support custom grid sizes [#44](https://github.com/contao-bootstrap/grid/pull/44) (@scuben)
 - Use custom template for grid element [#41](https://github.com/contao-bootstrap/grid/pull/41) (@RoflCopter24)

### Fixed

 - Prevent ambiguous field names in parent relation fixer [#43](https://github.com/contao-bootstrap/grid/pull/43)


[2.2.3] (2020-02-04)
--------------------

### Fixed

 - Do not register grid parent fixer for unsupported data container drivers [#40](https://github.com/contao-bootstrap/grid/issues/40)


[2.2.2] (2020-01-22)
--------------------

### Changed

 - Allow symfony/templating version ^5.0

### Fixed

 - Fix issue when duplicating form field [#39](https://github.com/contao-bootstrap/grid/issues/39)


[2.2.1] (2020-01-21)
-------------------

### Changed

 - Require `menatwork/contao-multicolumnwizard-bundle` instead of `menatwork/contao-multicolumnwizard`

### Fixed

 - Repair parent relation of grid elements now also works if children of a page are also copied
 - Only check nested data structures if the data container is a table


[2.2.0] (2020-01-18)
--------------------

### Added

 - Add option to define a grid in newslist and newsarchive modules to wrap each news article in a grid column
 - Add parent relation fixer which fix relations after an element or its parent got copied
 - Auto select closes grid start element/form field if type is changed


[2.1.2] (2019-11-11)
--------------------

### Fixed

 - Prevent double class attributes [#34](https://github.com/contao-bootstrap/grid/issues/34)
 - Do not silence all exceptions [#32](https://github.com/contao-bootstrap/grid/issues/32)
 - Do not add row class twiche [#33](https://github.com/contao-bootstrap/grid/issues/33)
 - Fix grid identifier for modules [#31](https://github.com/contao-bootstrap/grid/issues/31)


[2.1.1] (2019-06-11)
--------------------

### Fixed

 - Add missing closing div ([#30](https://github.com/contao-bootstrap/grid/issues/30))


[2.1.0] (2019-04-18)
--------------------

### Added

 - Allow to limit grid resets to a specific size ([#29](https://github.com/contao-bootstrap/grid/pull/29))
 - Support auto grid size ([#28](https://github.com/contao-bootstrap/grid/pull/28))
 - Support size suffixes for alignments ([#26](https://github.com/contao-bootstrap/grid/pull/26))
 - Tag response with grid id when grid element or module is used ([#18](https://github.com/contao-bootstrap/grid/issues/18))

### Fixed

 - Translation of grid parent is missing ([#23](https://github.com/contao-bootstrap/grid/issues/23))


2.0.8 (2019-01-29)
------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.7...2.0.8)

### Fixed

 - Fix deprecated warning using PHP 7.3 ([#24](https://github.com/contao-bootstrap/grid/issues/24))
 - Updated translations from transifex


2.0.7 (2018-08-31)
------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.6...2.0.7)

 - Fix broken grid gallery element not handling grid and image sizes.

2.0.6 (2018-08-28)
------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.5...2.0.6)

 - Rewrite gallery element for symfony 4 compatibility

2.0.5 (2018-08-24)
------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.4...2.0.5)

 - Rewrite grid content elements using netzmacht/contao-toolkit for symfony 4 compatibility
 - Implement a workaround so that form elements work again with symfony 4
 - Run composer-require-checker and fix issues.

2.0.4 (2018-07-27)
------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.3...hotfix/2.0.4)

 - Fix broken grid width label.
 - Fix broken grid parent select in override mode.
 - Fix template naming so that custom templates can be applied. Old templates will get removed in 2.1.0!
 - Fix label of grid parent field.

2.0.3 (2018-07-26)
------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.2...2.0.3)

 - Fix null column width.
 - Fix auto value selection. Always 0 was selected.
 - Add Contao 4.5 to the travis test matrix.

2.0.2 (2018-04-23)
------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.1...2.0.2)

 - Fix issue of attempt to load `GridModel` from the global namespace (#9).

2.0.1 (2018-01-29)
------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.0...2.0.1)

 - Catch error in grid gallery element if grid does not exist
 - Fix broken gallery image sorting
 - Fix grid order in the options
 - Fix img thumbnail for the gallery images.
 - Fix auto submitting of the grid input fields.

2.0.0 (2018-01-05)
------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.0-beta3...2.0.0)

 - Support MetaPalettes v2.0.

2.0.0-beta3 (2017-12-01)
------------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.0-beta2...2.0.0-beta3)

 - Make listener services public as Contao requires it.

2.0.0-beta2 (2017-10-23)
------------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.0-beta1...2.0.0-beta2)

 - Add grid content element
 - Fix strict type handling errors


2.0.0-beta1 (2017-09-27)
------------------------

[Full Changelog](https://github.com/contao-bootstrap/grid/compare/2.0.0-alpha1...2.0.0-beta1)

Implemented enhancements:

 - Require PHP 7.1
 - Support Bootstrap 4 beta changes
 - Complete english translation
 - Support import/export of the theme
 - Support easy themes
 - Added README.md
 - Added .gitattributes
 - Added CHANGELOG.md

Fixed bugs:

 - Grid row settings were not recognized
 - Frontend module did not support resets


[Unreleased]: https://github.com/contao-bootstrap/grid/compare/master...develop
[2.4.0]: https://github.com/contao-bootstrap/grid/compare/2.3.1...2.4.0
[2.3.1]: https://github.com/contao-bootstrap/grid/compare/2.3.0...2.3.1
[2.3.0]: https://github.com/contao-bootstrap/grid/compare/2.2.3...2.3.0
[2.2.2]: https://github.com/contao-bootstrap/grid/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/contao-bootstrap/grid/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/contao-bootstrap/grid/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/contao-bootstrap/grid/compare/2.1.2...2.2.0
[2.1.2]: https://github.com/contao-bootstrap/grid/compare/2.1.1...2.1.2
[2.1.2]: https://github.com/contao-bootstrap/grid/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/contao-bootstrap/grid/compare/2.0.8...2.1.0
