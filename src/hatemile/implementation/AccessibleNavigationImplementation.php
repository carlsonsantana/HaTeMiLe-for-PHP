<?php
/*
Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
 */

namespace hatemile\implementation;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'AccessibleNavigation.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'CommonFunctions.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'Configure.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'html',
    'HTMLDOMElement.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'html',
    'HTMLDOMParser.php'
));

use \hatemile\AccessibleNavigation;
use \hatemile\util\CommonFunctions;
use \hatemile\util\Configure;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;

/**
 * The AccessibleNavigationImplementation class is official implementation of
 * AccessibleNavigation interface.
 */
class AccessibleNavigationImplementation implements AccessibleNavigation
{

    /**
     * The id of list element that contains the description of shortcuts.
     * @var string
     */
    const ID_CONTAINER_SHORTCUTS = 'container-shortcuts';

    /**
     * The id of list element that contains the skippers.
     * @var string
     */
    const ID_CONTAINER_SKIPPERS = 'container-skippers';

    /**
     * The id of list element that contains the links for the headings.
     * @var string
     */
    const ID_CONTAINER_HEADING = 'container-heading';

    /**
     * The id of text of description of container of shortcuts descriptions.
     * @var string
     */
    const ID_TEXT_SHORTCUTS = 'text-shortcuts';

    /**
     * The id of text of description of container of heading links.
     * @var string
     */
    const ID_TEXT_HEADING = 'text-heading';

    /**
     * The HTML class of anchor of skipper.
     * @var string
     */
    const CLASS_SKIPPER_ANCHOR = 'skipper-anchor';

    /**
     * The HTML class of anchor of heading link.
     * @var string
     */
    const CLASS_HEADING_ANCHOR = 'heading-anchor';

    /**
     * The HTML class of element for show the long description of image.
     * @var string
     */
    const CLASS_LONG_DESCRIPTION_LINK = 'longdescription-link';

    /**
     * The name of attribute that link the list item element with the shortcut.
     * @var string
     */
    const DATA_ACCESS_KEY = 'data-shortcutdescriptionfor';

    /**
     * The name of attribute that links the anchor of skipper with the element.
     * @var string
     */
    const DATA_ANCHOR_FOR = 'data-anchorfor';

    /**
     * The name of attribute that indicates the level of heading of link.
     * @var string
     */
    const DATA_HEADING_LEVEL = 'data-headinglevel';

    /**
     * The name of attribute that links the anchor of heading link with heading.
     * @var string
     */
    const DATA_HEADING_ANCHOR_FOR = 'data-headinganchorfor';

    /**
     * The name of attribute that link the anchor of long description with the
     * image.
     * @var string
     */
    const DATA_LONG_DESCRIPTION_FOR_IMAGE = 'data-longdescriptionfor';

    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $parser;

    /**
     * The text of description of container of shortcuts descriptions.
     * @var string
     */
    protected $textShortcuts;

    /**
     * The browser shortcut prefix.
     * @var string
     */
    protected $prefix;

    /**
     * Standart browser prefix.
     * @var string
     */
    protected $standartPrefix;

    /**
     * The text of description of container of heading links.
     * @var string
     */
    protected $textHeading;

    /**
     * The prefix of content of long description.
     * @var string
     */
    protected $prefixLongDescriptionLink;

    /**
     * The suffix of content of long description.
     * @var string
     */
    protected $suffixLongDescriptionLink;

    /**
     * The prefix of generated ids.
     * @var string
     */
    protected $prefixId;

    /**
     * The skippers configured.
     * @var string[][]
     */
    protected $skippers;

    /**
     * The state that indicates if the container of skippers has added.
     * @var boolean
     */
    protected $listSkippersAdded;

    /**
     * The list element of skippers.
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $listSkippers;

    /**
     * The state that indicates if the sintatic heading of parser be validated.
     * @var boolean
     */
    protected $validateHeading;

    /**
     * The state that indicates if the sintatic heading of parser is correct.
     * @var boolean
     */
    protected $validHeading;

    /**
     * The list element of shortcuts.
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $listShortcuts;

    /**
     * The state that indicates if the list of shortcuts of page was added.
     * @var boolean
     */
    protected $listShortcutsAdded;

    /**
     * Initializes a new object that manipulate the accessibility of the
     * navigation of parser.
     * @param \hatemile\util\html\HTMLDOMParser $parser The HTML parser.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     * @param string $skipperFileName The file path of skippers configuration.
     * @param string $userAgent The user agent of the user.
     */
    public function __construct(
        HTMLDOMParser $parser,
        Configure $configure,
        $skipperFileName = null,
        $userAgent = null
    ) {
        $this->parser = $parser;
        $this->prefixId = $configure->getParameter('prefix-generated-ids');
        $this->textShortcuts = $configure->getParameter('text-shortcuts');
        $this->textHeading = $configure->getParameter('text-heading');
        $this->standartPrefix = $configure->getParameter(
            'text-standart-shortcut-prefix'
        );
        $this->prefixLongDescriptionLink = $configure->getParameter(
            'prefix-longdescription'
        );
        $this->suffixLongDescriptionLink = $configure->getParameter(
            'suffix-longdescription'
        );
        $this->skippers = $this->getSkippers($skipperFileName);
        $this->listShortcutsAdded = false;
        $this->listSkippersAdded = false;
        $this->validateHeading = false;
        $this->validHeading = false;
        $this->listSkippers = null;
        $this->listShortcuts = null;

        if ($userAgent !== null) {
            $userAgent = strtolower($userAgent);
            $opera = strpos($userAgent, 'opera') !== false;
            $mac = strpos($userAgent, 'mac') !== false;
            $konqueror = strpos($userAgent, 'konqueror') !== false;
            $spoofer = strpos($userAgent, 'spoofer') !== false;
            $safari = strpos($userAgent, 'applewebkit') !== false;
            $windows = strpos($userAgent, 'windows') !== false;
            $chrome = strpos($userAgent, 'chrome') !== false;
            $firefox = preg_match('/firefox\/[2-9]|minefield\/3/', $userAgent);
            $ie = (
                (strpos($userAgent, 'msie') !== false)
                || (strpos($userAgent, 'trident') !== false)
            );

            if ($opera) {
                $this->prefix = 'SHIFT + ESC';
            } elseif ($chrome && $mac && !$spoofer) {
                $this->prefix = 'CTRL + OPTION';
            } elseif ($safari && !$windows && !$spoofer) {
                $this->prefix = 'CTRL + ALT';
            } elseif (!$windows && ($safari || $mac || $konqueror)) {
                $this->prefix = 'CTRL';
            } elseif ($firefox) {
                $this->prefix = 'ALT + SHIFT';
            } elseif ($chrome || $ie) {
                $this->prefix = 'ALT';
            } else {
                $this->prefix = $this->standartPrefix;
            }
        } else {
            $this->prefix = $this->standartPrefix;
        }
    }

    /**
     * Returns the skippers of configuration.
     * @param string $fileName The file path of skippers configuration.
     * @return string[][] The skippers of configuration.
     */
    protected function getSkippers($fileName)
    {
        $skippers = array();
        if ($fileName === null) {
            $fileName = join(DIRECTORY_SEPARATOR, array(
                dirname(dirname(dirname(__FILE__))),
                'skippers.xml'
            ));
        }
        $file = new \DOMDocument();
        $file->load($fileName);
        $document = $file->documentElement;
        $childNodes = $document->childNodes;
        foreach ($childNodes as $child) {
            if (
                ($child instanceof \DOMElement)
                && (strtoupper($child->tagName) === 'SKIPPER')
                && ($child->hasAttribute('selector'))
                && ($child->hasAttribute('description'))
            ) {
                array_push(
                    $skippers,
                    array(
                        'selector' => $child->getAttribute('selector'),
                        'description' => $child->getAttribute('description'),
                        'shortcut' => $child->getAttribute('shortcut')
                    )
                );
            }
        }

        return $skippers;
    }

    /**
     * Returns the description of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element with
     * description.
     * @return string The description of element.
     */
    protected function getDescription(HTMLDOMElement $element)
    {
        if ($element->hasAttribute('title')) {
            $description = $element->getAttribute('title');
        } elseif ($element->hasAttribute('aria-label')) {
            $description = $element->getAttribute('aria-label');
        } elseif ($element->hasAttribute('alt')) {
            $description = $element->getAttribute('alt');
        } elseif ($element->hasAttribute('label')) {
            $description = $element->getAttribute('label');
        } elseif (
            ($element->hasAttribute('aria-labelledby'))
            || ($element->hasAttribute('aria-describedby'))
        ) {
            if ($element->hasAttribute('aria-labelledby')) {
                $descriptionIds = preg_split(
                    '/[ \n\t\r]+/',
                    $element->getAttribute('aria-labelledby')
                );
            } else {
                $descriptionIds = preg_split(
                    '/[ \n\t\r]+/',
                    $element->getAttribute('aria-describedby')
                );
            }
            foreach ($descriptionIds as $descriptionId) {
                $elementDescription = $this->parser->find(
                    '#' . $descriptionId
                )->firstResult();
                if ($elementDescription !== null) {
                    $description = $elementDescription->getTextContent();
                    break;
                }
            }
        } elseif (
            ($element->getTagName() === 'INPUT')
            && ($element->hasAttribute('type'))
        ) {
            $type = strtolower($element->getAttribute('type'));
            if (
                (
                    ($type === 'button')
                    || ($type === 'submit')
                    || ($type === 'reset')
                )
                && ($element->hasAttribute('value'))
            ) {
                $description = $element->getAttribute('value');
            }
        }
        if (empty($description)) {
            $description = $element->getTextContent();
        }
        return \trim(\preg_replace("/[ \n\r\t]+/", ' ', $description));
    }

    /**
     * Generate the list of shortcuts of page.
     * @return \hatemile\util\html\HTMLDOMElement The list of shortcuts of page.
     */
    protected function generateListShortcuts()
    {
        $container = $this->parser->find(
            '#' . AccessibleNavigationImplementation::ID_CONTAINER_SHORTCUTS
        )->firstResult();
        $htmlList = null;
        if ($container === null) {
            $local = $this->parser->find('body')->firstResult();
            if ($local !== null) {
                $container = $this->parser->createElement('div');
                $container->setAttribute(
                    'id',
                    AccessibleNavigationImplementation::ID_CONTAINER_SHORTCUTS
                );

                $textContainer = $this->parser->createElement('span');
                $textContainer->setAttribute(
                    'id',
                    AccessibleNavigationImplementation::ID_TEXT_SHORTCUTS
                );
                $textContainer->appendText($this->textShortcuts);

                $container->appendElement($textContainer);
                $local->appendElement($container);
            }
        }
        if ($container !== null) {
            $htmlList = $this->parser->find($container)->findChildren(
                'ul'
            )->firstResult();
            if ($htmlList === null) {
                $htmlList = $this->parser->createElement('ul');
                $container->appendElement($htmlList);
            }
        }
        $this->listShortcutsAdded = true;

        return $htmlList;
    }

    /**
     * Generate the list of skippers of page.
     * @return \hatemile\util\html\HTMLDOMElement The list of skippers of page.
     */
    protected function generateListSkippers()
    {
        $container = $this->parser->find(
            '#' . AccessibleNavigationImplementation::ID_CONTAINER_SKIPPERS
        )->firstResult();
        $htmlList = null;
        if ($container === null) {
            $local = $this->parser->find('body')->firstResult();
            if ($local !== null) {
                $container = $this->parser->createElement('div');
                $container->setAttribute(
                    'id',
                    AccessibleNavigationImplementation::ID_CONTAINER_SKIPPERS
                );
                $local->getFirstElementChild()->insertBefore($container);
            }
        }
        if ($container !== null) {
            $htmlList = $this->parser->find($container)->findChildren(
                'ul'
            )->firstResult();
            if ($htmlList == null) {
                $htmlList = $this->parser->createElement('ul');
                $container->appendElement($htmlList);
            }
        }
        $this->listSkippersAdded = true;
        return $htmlList;
    }

    /**
     * Generate the list of heading links of page.
     * @return \hatemile\util\html\HTMLDOMElement The list of heading links of
     * page.
     */
    protected function generateListHeading()
    {
        $container = $this->parser->find(
            '#' . AccessibleNavigationImplementation::ID_CONTAINER_HEADING
        )->firstResult();
        $htmlList = null;
        if ($container === null) {
            $local = $this->parser->find('body')->firstResult();
            if ($local !== null) {
                $container = $this->parser->createElement('div');
                $container->setAttribute(
                    'id',
                    AccessibleNavigationImplementation::ID_CONTAINER_HEADING
                );

                $textContainer = $this->parser->createElement('span');
                $textContainer->setAttribute(
                    'id',
                    AccessibleNavigationImplementation::ID_TEXT_HEADING
                );
                $textContainer->appendText($this->textHeading);

                $container->appendElement($textContainer);
                $local->appendElement($container);
            }
        }
        if ($container !== null) {
            $htmlList = $this->parser->find($container)->findChildren(
                'ol'
            )->firstResult();
            if ($htmlList === null) {
                $htmlList = $this->parser->createElement('ol');
                $container->appendElement($htmlList);
            }
        }
        return $htmlList;
    }

    /**
     * Returns the level of heading.
     * @param \hatemile\util\html\HTMLDOMElement $element The heading.
     * @return integer The level of heading.
     */
    protected function getHeadingLevel(HTMLDOMElement $element)
    {
        $tag = $element->getTagName();
        if ($tag === 'H1') {
            return 1;
        } elseif ($tag === 'H2') {
            return 2;
        } elseif ($tag === 'H3') {
            return 3;
        } elseif ($tag === 'H4') {
            return 4;
        } elseif ($tag === 'H5') {
            return 5;
        } elseif ($tag === 'H6') {
            return 6;
        } else {
            return -1;
        }
    }

    /**
     * Inform if the headings of page are sintatic correct.
     * @return boolean True if the headings of page are sintatic correct or
     * false if not.
     */
    protected function isValidHeading()
    {
        $elements = $this->parser->find('h1,h2,h3,h4,h5,h6')->listResults();
        $lastLevel = 0;
        $countMainHeading = 0;
        $this->validateHeading = true;
        foreach ($elements as $element) {
            $level = $this->getHeadingLevel($element);
            if ($level === 1) {
                if ($countMainHeading === 1) {
                    return false;
                } else {
                    $countMainHeading = 1;
                }
            }
            if (($level - $lastLevel) > 1) {
                return false;
            }
            $lastLevel = $level;
        }
        return true;
    }

    /**
     * Generate an anchor for the element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     * @param string $dataAttribute The name of attribute that links the element
     * with the anchor.
     * @param string $anchorClass The HTML class of anchor.
     * @return \hatemile\util\html\HTMLDOMElement The anchor.
     */
    protected function generateAnchorFor(
        HTMLDOMElement $element,
        $dataAttribute,
        $anchorClass
    ) {
        CommonFunctions::generateId($element, $this->prefixId);
        $anchor = null;
        $at = '[' . $dataAttribute . '="' . $element->getAttribute('id') . '"]';
        if ($this->parser->find($at)->firstResult() === null) {
            if ($element->getTagName() === 'A') {
                $anchor = $element;
            } else {
                $anchor = $this->parser->createElement('a');
                CommonFunctions::generateId($anchor, $this->prefixId);
                $anchor->setAttribute('class', $anchorClass);
                $element->insertBefore($anchor);
            }
            if (!$anchor->hasAttribute('name')) {
                $anchor->setAttribute('name', $anchor->getAttribute('id'));
            }
            $anchor->setAttribute($dataAttribute, $element->getAttribute('id'));
        }
        return $anchor;
    }

    /**
     * Replace the shortcut of elements, that has the shortcut passed.
     * @param string $shortcut The shortcut.
     */
    protected function freeShortcut($shortcut)
    {
        $alphaNumbers = '1234567890abcdefghijklmnopqrstuvwxyz';
        $elements = $this->parser->find('[accesskey]')->listResults();
        foreach ($elements as $element) {
            $shortcuts = strtolower($element->getAttribute('accesskey'));
            if (CommonFunctions::inList($shortcuts, $shortcut)) {
                $length = strlen($alphaNumbers);
                for ($i = 0; $i < $length; $i++) {
                    $key = substr($alphaNumbers, 0, 1);
                    $found = true;
                    foreach ($elements as $elementWithShortcuts) {
                        $shortcuts = strtolower(
                            $elementWithShortcuts->getAttribute('accesskey')
                        );
                        if (CommonFunctions::inList($shortcuts, $key)) {
                            $found = false;
                            break;
                        }
                    }
                    if ($found) {
                        $element->setAttribute('accesskey', $key);
                        break;
                    }
                }
                if ($found) {
                    break;
                }
            }
        }
    }

    public function fixShortcut(HTMLDOMElement $element)
    {
        if ($element->hasAttribute('accesskey')) {
            $description = $this->getDescription($element);
            if (!$element->hasAttribute('title')) {
                $element->setAttribute('title', $description);
            }

            if (!$this->listShortcutsAdded) {
                $this->listShortcuts = $this->generateListShortcuts();
            }

            if ($this->listShortcuts !== null) {
                $keys = preg_split(
                    '/[ \n\t\r]+/',
                    $element->getAttribute('accesskey')
                );
                foreach ($keys as $key) {
                    $key = strtoupper($key);
                    $attr = (
                        '[' .
                        AccessibleNavigationImplementation::DATA_ACCESS_KEY .
                        '="' .
                        $key .
                        '"]'
                    );
                    if ($this->parser->find(
                        $this->listShortcuts
                    )->findChildren($attr)->firstResult() === null) {
                        $item = $this->parser->createElement('li');
                        $item->setAttribute(
                            AccessibleNavigationImplementation::DATA_ACCESS_KEY,
                            $key
                        );
                        $item->appendText(
                            $this->prefix . ' + ' . $key . ': ' . $description
                        );
                        $this->listShortcuts->appendElement($item);
                    }
                }
            }
        }
    }

    public function fixShortcuts()
    {
        $elements = $this->parser->find('[accesskey]')->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->fixShortcut($element);
            }
        }
    }

    public function fixSkipper(HTMLDOMElement $element)
    {
        $skipper = null;
        foreach ($this->skippers as $auxiliarSkipper) {
            $elements = $this->parser->find(
                $auxiliarSkipper['selector']
            )->listResults();
            foreach ($elements as $auxiliarElement) {
                if ($element->equals($auxiliarElement)) {
                    $skipper = $auxiliarSkipper;
                }
            }
        }
        if ($skipper !== null) {
            if (!$this->listSkippersAdded) {
                $this->listSkippers = $this->generateListSkippers();
            }
            if ($this->listSkippers !== null) {
                $anchor = $this->generateAnchorFor(
                    $element,
                    AccessibleNavigationImplementation::DATA_ANCHOR_FOR,
                    AccessibleNavigationImplementation::CLASS_SKIPPER_ANCHOR
                );
                if ($anchor !== null) {
                    $itemLink = $this->parser->createElement('li');
                    $link = $this->parser->createElement('a');
                    $link->setAttribute(
                        'href',
                        '#' . $anchor->getAttribute('name')
                    );
                    $link->appendText($skipper['description']);

                    $this->freeShortcut($skipper['shortcut']);
                    $link->setAttribute('accesskey', $skipper['shortcut']);

                    CommonFunctions::generateId($link, $this->prefixId);

                    $itemLink->appendElement($link);
                    $this->listSkippers->appendElement($itemLink);
                }
            }
        }
    }

    public function fixSkippers()
    {
        foreach ($this->skippers as $skipper) {
            $elements = $this->parser->find(
                $skipper['selector']
            )->listResults();
            foreach ($elements as $element) {
                if (CommonFunctions::isValidElement($element)) {
                    $this->fixSkipper($element);
                }
            }
        }
    }

    public function fixHeading(HTMLDOMElement $element)
    {
        if (!$this->validateHeading) {
            $this->validHeading = $this->isValidHeading();
        }
        if ($this->validHeading) {
            $anchor = $this->generateAnchorFor(
                $element,
                AccessibleNavigationImplementation::DATA_HEADING_ANCHOR_FOR,
                AccessibleNavigationImplementation::CLASS_HEADING_ANCHOR
            );
            if ($anchor !== null) {
                $list = null;
                $level = $this->getHeadingLevel($element);
                if ($level === 1) {
                    $list = $this->generateListHeading();
                } else {
                    $attr = (
                        '['
                        . AccessibleNavigationImplementation::DATA_HEADING_LEVEL
                        . '="'
                        . ((string) ($level - 1))
                        . '"]'
                    );
                    $superItem = $this->parser->find(
                        '#' .
                        AccessibleNavigationImplementation::ID_CONTAINER_HEADING
                    )->findDescendants($attr)->lastResult();
                    if ($superItem !== null) {
                        $list = $this->parser->find($superItem)->findChildren(
                            'ol'
                        )->firstResult();
                        if ($list === null) {
                            $list = $this->parser->createElement('ol');
                            $superItem->appendElement($list);
                        }
                    }
                }
                if ($list !== null) {
                    $item = $this->parser->createElement('li');
                    $item->setAttribute(
                        AccessibleNavigationImplementation::DATA_HEADING_LEVEL,
                        ((string) ($level))
                    );

                    $link = $this->parser->createElement('a');
                    $link->setAttribute(
                        'href',
                        '#' . $anchor->getAttribute('name')
                    );
                    $link->appendText($element->getTextContent());

                    $item->appendElement($link);
                    $list->appendElement($item);
                }
            }
        }
    }

    public function fixHeadings()
    {
        $elements = $this->parser->find('h1,h2,h3,h4,h5,h6')->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->fixHeading($element);
            }
        }
    }

    public function fixLongDescription(HTMLDOMElement $element)
    {
        if ($element->hasAttribute('longdesc')) {
            CommonFunctions::generateId($element, $this->prefixId);
            $id = $element->getAttribute('id');
            $attr = (
                '[' .
                AccessibleNavigationImplementation
                        ::DATA_LONG_DESCRIPTION_FOR_IMAGE .
                '="' .
                $id .
                '"]'
            );
            if ($this->parser->find($attr)->firstResult() === null) {
                if ($element->hasAttribute('alt')) {
                    $text = (
                        $this->prefixLongDescriptionLink
                        . ' '
                        . $element->getAttribute('alt')
                        . ' '
                        . $this->suffixLongDescriptionLink
                    );
                } else {
                    $text = (
                        $this->prefixLongDescriptionLink
                        . ' '
                        . $this->suffixLongDescriptionLink
                    );
                }
                $anchor = $this->parser->createElement('a');
                $anchor->setAttribute(
                    'href',
                    $element->getAttribute('longdesc')
                );
                $anchor->setAttribute('target', '_blank');
                $anchor->setAttribute(
                    AccessibleNavigationImplementation
                            ::DATA_LONG_DESCRIPTION_FOR_IMAGE,
                    $id
                );
                $anchor->setAttribute(
                    'class',
                    AccessibleNavigationImplementation
                            ::CLASS_LONG_DESCRIPTION_LINK
                );
                $anchor->appendText(\trim($text));
                $element->insertAfter($anchor);
            }
        }
    }

    public function fixLongDescriptions()
    {
        $elements = $this->parser->find('[longdesc]')->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->fixLongDescription($element);
            }
        }
    }
}
