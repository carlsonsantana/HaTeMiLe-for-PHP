HaTeMiLe for PHP
================

HaTeMiLe (HTML Accessible) is a library that can convert a HTML code in a HTML code more accessible.

## Accessibility solutions

* [Associate HTML elements](https://github.com/hatemile/hatemile-for-php/wiki/Associate-HTML-elements);
* [Provide a polyfill to CSS Speech and CSS Aural properties](https://github.com/hatemile/hatemile-for-php/wiki/Provide-a-polyfill-to-CSS-Speech-and-CSS-Aural-properties);
* [Display inacessible informations of page](https://github.com/hatemile/hatemile-for-php/wiki/Display-inacessible-informations-of-page);
* [Enable all functionality of page available from a keyboard](https://github.com/hatemile/hatemile-for-php/wiki/Enable-all-functionality-of-page-available-from-a-keyboard);
* [Improve the acessibility of forms](https://github.com/hatemile/hatemile-for-php/wiki/Improve-the-acessibility-of-forms);
* [Provide accessibility resources to navigate](https://github.com/hatemile/hatemile-for-php/wiki/Provide-accessibility-resources-to-navigate).

## Documentation

To generate the full API documentation of HaTeMiLe of PHP:

1. [Install phpDocumentor](http://docs.phpdoc.org/getting-started/installing.html);
2. [Execute the phpDocumentor in HaTeMiLe of PHP directory](http://docs.phpdoc.org/guides/running-phpdocumentor.html).

## Usage

Import all needed classes:

```php
require_once './phpQuery/phpQuery/phpQuery.php';
require_once './hatemile-for-php/src/hatemile/implementation/AccessibleAssociationImplementation.php';
require_once './hatemile-for-php/src/hatemile/implementation/AccessibleCSSImplementation.php';
require_once './hatemile-for-php/src/hatemile/implementation/AccessibleDisplayScreenReaderImplementation.php';
require_once './hatemile-for-php/src/hatemile/implementation/AccessibleEventImplementation.php';
require_once './hatemile-for-php/src/hatemile/implementation/AccessibleFormImplementation.php';
require_once './hatemile-for-php/src/hatemile/implementation/AccessibleNavigationImplementation.php';
require_once './hatemile-for-php/src/hatemile/util/Configure.php';
require_once './hatemile-for-php/src/hatemile/util/css/phpcssparser/PHPCSSParser.php';
require_once './hatemile-for-php/src/hatemile/util/html/phpquery/PhpQueryHTMLDOMParser.php';

use hatemile\implementation\AccessibleAssociationImplementation;
use hatemile\implementation\AccessibleCSSImplementation;
use hatemile\implementation\AccessibleDisplayScreenReaderImplementation;
use hatemile\implementation\AccessibleEventImplementation;
use hatemile\implementation\AccessibleFormImplementation;
use hatemile\implementation\AccessibleNavigationImplementation;
use hatemile\util\Configure;
use hatemile\util\css\phpcssparser\PHPCSSParser;
use hatemile\util\html\phpquery\PhpQueryHTMLDOMParser;
```

Instanciate the configuration, the parsers and solution classes and execute them:

```java
$configure = new Configure();

$parser = new PhpQueryHTMLDOMParser($content);
$cssParser = new PHPCSSParser($parser);

$accessibleEvent = new AccessibleEventImplementation($parser, $configure);
$accessibleCSS = new AccessibleCSSImplementation(
    $parser,
    $cssParser,
    $configure
);
$accessibleForm = new AccessibleFormImplementation($parser, $configure);
$accessibleNavigation = new AccessibleNavigationImplementation(
    $parser,
    $configure
);
$accessibleAssociation = new AccessibleAssociationImplementation(
    $parser,
    $configure
);
$accessibleDisplay = new AccessibleDisplayScreenReaderImplementation(
    $parser,
    $configure
);

$accessibleEvent->makeAccessibleAllDragandDropEvents();
$accessibleEvent->makeAccessibleAllClickEvents();
$accessibleEvent->makeAccessibleAllHoverEvents();

$accessibleForm->markAllAutoCompleteFields();
$accessibleForm->markAllRequiredFields();
$accessibleForm->markAllRangeFields();
$accessibleForm->markAllInvalidFields();

$accessibleNavigation->provideNavigationByAllHeadings();
$accessibleNavigation->provideNavigationByAllSkippers();
$accessibleNavigation->provideNavigationToAllLongDescriptions();

$accessibleAssociation->associateAllDataCellsWithHeaderCells();
$accessibleAssociation->associateAllLabelsWithFields();

$accessibleDisplay->displayAllShortcuts();
$accessibleDisplay->displayAllRoles();
$accessibleDisplay->displayAllCellHeaders();
$accessibleDisplay->displayAllWAIARIAStates();
$accessibleDisplay->displayAllLinksAttributes();
$accessibleDisplay->displayAllTitles();
$accessibleDisplay->displayAllLanguages();
$accessibleDisplay->displayAllAlternativeTextImages();

$accessibleNavigation->provideNavigationByAllSkippers();
$accessibleDisplay->displayAllShortcuts();
    
$accessibleCSS->provideAllSpeakProperties();

echo $parser->getHTML();
```

## Contributing

If you want contribute with HaTeMiLe for PHP, read [contributing guidelines](CONTRIBUTING.md).

## See also
* [HaTeMiLe for CSS](https://github.com/hatemile/hatemile-for-css)
* [HaTeMiLe for JavaScript](https://github.com/hatemile/hatemile-for-javascript)
* [HaTeMiLe for Java](https://github.com/hatemile/hatemile-for-java)
* [HaTeMiLe for Python](https://github.com/hatemile/hatemile-for-python)
* [HaTeMiLe for Ruby](https://github.com/hatemile/hatemile-for-ruby)
