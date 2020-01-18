
Changelog
=========

[Unreleased]
------------

[2.2.0] (2020-01-18)

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
[2.1.2]: https://github.com/contao-bootstrap/grid/compare/2.1.2...2.2.0
[2.1.2]: https://github.com/contao-bootstrap/grid/compare/2.1.1...2.1.2
[2.1.2]: https://github.com/contao-bootstrap/grid/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/contao-bootstrap/grid/compare/2.0.8...2.1.0
