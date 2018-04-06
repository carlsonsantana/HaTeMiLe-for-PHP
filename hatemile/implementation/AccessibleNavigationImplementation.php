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

require_once dirname(__FILE__) . '/../util/html/HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../util/Configure.php';
require_once dirname(__FILE__) . '/../util/CommonFunctions.php';
require_once dirname(__FILE__) . '/../util/Skipper.php';
require_once dirname(__FILE__) . '/../util/html/HTMLDOMElement.php';
require_once dirname(__FILE__) . '/../AccessibleNavigation.php';

use \hatemile\util\html\HTMLDOMParser;
use \hatemile\util\Configure;
use \hatemile\util\CommonFunctions;
use \hatemile\util\Skipper;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\AccessibleNavigation;

/**
 * The AccessibleNavigationImplementation class is official implementation of
 * AccessibleNavigation interface.
 */
class AccessibleNavigationImplementation implements AccessibleNavigation
{
    
    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $parser;
    
    /**
     * The id of list element that contains the description of shortcuts.
     * @var string
     */
    protected $idContainerShortcuts;
    
    /**
     * The id of text of description of container of shortcuts descriptions.
     * @var string
     */
    protected $idTextShortcuts;
    
    /**
     * The text of description of container of shortcuts descriptions.
     * @var string
     */
    protected $textShortcuts;
    
    /**
     * The name of attribute that link the list item element with the shortcut.
     * @var string
     */
    protected $dataAccessKey;
    
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
     * The id of list element that contains the skippers.
     * @var string
     */
    protected $idContainerSkippers;
    
    /**
     * The id of list element that contains the links for the headings.
     * @var string
     */
    protected $idContainerHeading;
    
    /**
     * The id of text of description of container of heading links.
     * @var string
     */
    protected $idTextHeading;
    
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
     * @var \hatemile\util\Skipper[]
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
     * The name of attribute that links the anchor of skipper with the element.
     * @var string
     */
    protected $dataAnchorFor;
    
    /**
     * The HTML class of anchor of skipper.
     * @var string
     */
    protected $classSkipperAnchor;
    
    /**
     * The HTML class of anchor of heading link.
     * @var string
     */
    protected $classHeadingAnchor;
    
    /**
     * The HTML class of element for show the long description of image.
     * @var string
     */
    protected $classLongDescriptionLink;
    
    /**
     * The name of attribute that links the anchor of heading link with heading.
     * @var string
     */
    protected $dataHeadingAnchorFor;
    
    /**
     * The name of attribute that indicates the level of heading of link.
     * @var string
     */
    protected $dataHeadingLevel;
    
    /**
     * The name of attribute that link the anchor of long description with the
     * image.
     * @var string
     */
    protected $dataLongDescriptionForImage;
    
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
     * @param string $userAgent The user agent of the user.
     */
    public function __construct(HTMLDOMParser $parser, Configure $configure, $userAgent = null)
    {
        $this->parser = $parser;
        $this->idContainerShortcuts = 'container-shortcuts';
        $this->idContainerSkippers = 'container-skippers';
        $this->idContainerHeading = 'container-heading';
        $this->idTextShortcuts = 'text-shortcuts';
        $this->idTextHeading = 'text-heading';
        $this->classSkipperAnchor = 'skipper-anchor';
        $this->classHeadingAnchor = 'heading-anchor';
        $this->classLongDescriptionLink = 'longdescription-link';
        $this->dataAccessKey = 'data-shortcutdescriptionfor';
        $this->dataAnchorFor = 'data-anchorfor';
        $this->dataHeadingAnchorFor = 'data-headinganchorfor';
        $this->dataHeadingLevel = 'data-headinglevel';
        $this->dataLongDescriptionForImage = 'data-longdescriptionfor';
        $this->prefixId = $configure->getParameter('prefix-generated-ids');
        $this->textShortcuts = $configure->getParameter('text-shortcuts');
        $this->textHeading = $configure->getParameter('text-heading');
        $this->standartPrefix = $configure->getParameter('text-standart-shortcut-prefix');
        $this->prefixLongDescriptionLink = $configure->getParameter('prefix-longdescription');
        $this->suffixLongDescriptionLink = $configure->getParameter('suffix-longdescription');
        $this->skippers = $configure->getSkippers();
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
            $ie = (strpos($userAgent, 'msie') !== false) || (strpos($userAgent, 'trident') !== false);
            
            if ($opera) {
                $this->prefix = 'SHIFT + ESC';
            } else if ($chrome && $mac && !$spoofer) {
                $this->prefix = 'CTRL + OPTION';
            } else if ($safari && !$windows && !$spoofer) {
                $this->prefix = 'CTRL + ALT';
            } else if (!$windows && ($safari || $mac || $konqueror)) {
                $this->prefix = 'CTRL';
            } else if ($firefox) {
                $this->prefix = 'ALT + SHIFT';
            } else if ($chrome || $ie) {
                $this->prefix = 'ALT';
            } else {
                $this->prefix = $this->standartPrefix;
            }
        } else {
            $this->prefix = $this->standartPrefix;
        }
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
        } else if ($element->hasAttribute('aria-label')) {
            $description = $element->getAttribute('aria-label');
        } else if ($element->hasAttribute('alt')) {
            $description = $element->getAttribute('alt');
        } else if ($element->hasAttribute('label')) {
            $description = $element->getAttribute('label');
        } else if (($element->hasAttribute('aria-labelledby'))
                || ($element->hasAttribute('aria-describedby'))) {
            if ($element->hasAttribute('aria-labelledby')) {
                $descriptionIds = preg_split("/[ \n\t\r]+/", $element->getAttribute('aria-labelledby'));
            } else {
                $descriptionIds = preg_split("/[ \n\t\r]+/", $element->getAttribute('aria-describedby'));
            }
            foreach ($descriptionIds as $descriptionId) {
                $elementDescription = $this->parser->find('#' . $descriptionId)->firstResult();
                if ($elementDescription !== null) {
                    $description = $elementDescription->getTextContent();
                    break;
                }
            }
        } else if (($element->getTagName() === 'INPUT') && ($element->hasAttribute('type'))) {
            $type = strtolower($element->getAttribute('type'));
            if ((($type === 'button') || ($type === 'submit') || ($type === 'reset'))
                    && ($element->hasAttribute('value'))) {
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
        $container = $this->parser->find('#' . $this->idContainerShortcuts)->firstResult();
        $htmlList = null;
        if ($container === null) {
            $local = $this->parser->find('body')->firstResult();
            if ($local !== null) {
                $container = $this->parser->createElement('div');
                $container->setAttribute('id', $this->idContainerShortcuts);
                
                $textContainer = $this->parser->createElement('span');
                $textContainer->setAttribute('id', $this->idTextShortcuts);
                $textContainer->appendText($this->textShortcuts);
                
                $container->appendElement($textContainer);
                $local->appendElement($container);
                
                $this->executeFixSkipper($container);
                $this->executeFixSkipper($textContainer);
            }
        }
        if ($container !== null) {
            $htmlList = $this->parser->find($container)->findChildren('ul')->firstResult();
            if ($htmlList === null) {
                $htmlList = $this->parser->createElement('ul');
                $container->appendElement($htmlList);
            }
            $this->executeFixSkipper($htmlList);
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
        $container = $this->parser->find('#' . $this->idContainerSkippers)->firstResult();
        $htmlList = null;
        if ($container === null) {
            $local = $this->parser->find('body')->firstResult();
            if ($local !== null) {
                $container = $this->parser->createElement('div');
                $container->setAttribute('id', $this->idContainerSkippers);
                $local->getFirstElementChild()->insertBefore($container);
            }
        }
        if ($container !== null) {
            $htmlList = $this->parser->find($container)->findChildren('ul')->firstResult();
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
     * @return \hatemile\util\html\HTMLDOMElement The list of heading links of page.
     */
    protected function generateListHeading()
    {
        $container = $this->parser->find('#' . $this->idContainerHeading)->firstResult();
        $htmlList = null;
        if ($container === null) {
            $local = $this->parser->find('body')->firstResult();
            if ($local !== null) {
                $container = $this->parser->createElement('div');
                $container->setAttribute('id', $this->idContainerHeading);
                
                $textContainer = $this->parser->createElement('span');
                $textContainer->setAttribute('id', $this->idTextHeading);
                $textContainer->appendText($this->textHeading);
                
                $container->appendElement($textContainer);
                $local->appendElement($container);
                
                $this->executeFixSkipper($container);
                $this->executeFixSkipper($textContainer);
            }
        }
        if ($container !== null) {
            $htmlList = $this->parser->find($container)->findChildren('ol')->firstResult();
            if ($htmlList === null) {
                $htmlList = $this->parser->createElement('ol');
                $container->appendElement($htmlList);
            }
            $this->executeFixSkipper($htmlList);
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
        } else if ($tag === 'H2') {
            return 2;
        } else if ($tag === 'H3') {
            return 3;
        } else if ($tag === 'H4') {
            return 4;
        } else if ($tag === 'H5') {
            return 5;
        } else if ($tag === 'H6') {
            return 6;
        } else {
            return -1;
        }
    }
    
    /**
     * Inform if the headings of page are sintatic correct.
     * @return boolean True if the headings of page are sintatic correct or false if not.
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
     * @param string $dataAttribute The name of attribute that links the element with
     * the anchor.
     * @param string $anchorClass The HTML class of anchor.
     * @return \hatemile\util\html\HTMLDOMElement The anchor.
     */
    protected function generateAnchorFor(HTMLDOMElement $element, $dataAttribute, $anchorClass)
    {
        CommonFunctions::generateId($element, $this->prefixId);
        $anchor = null;
        if ($this->parser->find('[' . $dataAttribute . '="' . $element->getAttribute('id') . '"]')->firstResult() === null) {
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
                for ($i = 0, $length = strlen($alphaNumbers); $i < $length; $i++) {
                    $key = substr($alphaNumbers, 0, 1);
                    $found = true;
                    foreach ($elements as $elementWithShortcuts) {
                        $shortcuts = strtolower($elementWithShortcuts->getAttribute('accesskey'));
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
    
    /**
     * Call fixSkipper method for element, if the page has the container of
     * skippers.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function executeFixSkipper(HTMLDOMElement $element)
    {
        if ($this->listSkippers !== null) {
            foreach ($this->skippers as $skipper) {
                $compareElements = $this->parser->find($skipper->getSelector())->listResults();
                foreach ($compareElements as $compareElement) {
                    if ($compareElement->getData() === $element->getData()) {
                        $this->fixSkipper($element, $skipper);
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Call fixShortcut method for element, if the page has the container of
     * shortcuts.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function executeFixShortcut(HTMLDOMElement $element)
    {
        if ($this->listShortcuts !== null) {
            $this->fixShortcut($element);
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
                $keys = preg_split("/[ \n\t\r]+/", $element->getAttribute('accesskey'));
                foreach ($keys as $key) {
                    $key = strtoupper($key);
                    if ($this->parser->find($this->listShortcuts)
                            ->findChildren('[' . $this->dataAccessKey . '="' . $key . '"]')->firstResult() === null) {
                        $item = $this->parser->createElement('li');
                        $item->setAttribute($this->dataAccessKey, $key);
                        $item->appendText($this->prefix . ' + ' . $key . ': ' . $description);
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
    
    public function fixSkipper(HTMLDOMElement $element, Skipper $skipper)
    {
        if (!$this->listSkippersAdded) {
            $this->listSkippers = $this->generateListSkippers();
        }
        if ($this->listSkippers !== null) {
            $anchor = $this->generateAnchorFor($element, $this->dataAnchorFor
                    , $this->classSkipperAnchor);
            if ($anchor !== null) {
                $itemLink = $this->parser->createElement('li');
                $link = $this->parser->createElement('a');
                $link->setAttribute('href', '#' . $anchor->getAttribute('name'));
                $link->appendText($skipper->getDefaultText());
                
                $shortcuts = $skipper->getShortcuts();
                if (!empty($shortcuts)) {
                    $shortcut = $shortcuts[0];
                    if (!empty($shortcut)) {
                        $this->freeShortcut($shortcut);
                        $link->setAttribute('accesskey', $shortcut);
                    }
                }
                CommonFunctions::generateId($link, $this->prefixId);
                
                $itemLink->appendElement($link);
                $this->listSkippers->appendElement($itemLink);
                
                $this->executeFixShortcut($link);
            }
        }
    }

    public function fixSkippers()
    {
        foreach ($this->skippers as $skipper) {
            $elements = $this->parser->find($skipper->getSelector())->listResults();
            $count = sizeof($elements) > 1;
            if ($count) {
                $index = 1;
            }
            $shortcuts = $skipper->getShortcuts();
            foreach ($elements as $element) {
                if (CommonFunctions::isValidElement($element)) {
                    if ($count) {
                        $defaultText = $skipper->getDefaultText() . ' ' . ((string) ($index++));
                    } else {
                        $defaultText = $skipper->getDefaultText();
                    }
                    if (sizeof($shortcuts) > 0) {
                        $this->fixSkipper($element, new Skipper($skipper->getSelector()
                                , $defaultText, $shortcuts[sizeof($shortcuts) - 1]));
                        unset($shortcuts[sizeof($shortcuts) - 1]);
                    } else {
                        $this->fixSkipper($element, new Skipper($skipper->getSelector()
                                , $defaultText, ''));
                    }
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
            $anchor = $this->generateAnchorFor($element, $this->dataHeadingAnchorFor, $this->classHeadingAnchor);
            if ($anchor !== null) {
                $list = null;
                $level = $this->getHeadingLevel($element);
                if ($level === 1) {
                    $list = $this->generateListHeading();
                } else {
                    $superItem = $this->parser->find('#' . $this->idContainerHeading)
                            ->findDescendants('[' . $this->dataHeadingLevel . '="' . ((string) ($level - 1)) . '"]')->lastResult();
                    if ($superItem !== null) {
                        $list = $this->parser->find($superItem)->findChildren('ol')->firstResult();
                        if ($list === null) {
                            $list = $this->parser->createElement('ol');
                            $superItem->appendElement($list);
                        }
                    }
                }
                if ($list !== null) {
                    $item = $this->parser->createElement('li');
                    $item->setAttribute($this->dataHeadingLevel, ((string) ($level)));
                    
                    $link = $this->parser->createElement('a');
                    $link->setAttribute('href', '#' . $anchor->getAttribute('name'));
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
            if ($this->parser->find('[' . $this->dataLongDescriptionForImage . '="' . $id . '"]')
                    ->firstResult() === null) {
                if ($element->hasAttribute('alt')) {
                    $text = $this->prefixLongDescriptionLink . ' ' . $element->getAttribute('alt')
                            . ' ' . $this->suffixLongDescriptionLink;
                } else {
                    $text = $this->prefixLongDescriptionLink . ' ' . $this->suffixLongDescriptionLink;
                }
                $anchor = $this->parser->createElement('a');
                $anchor->setAttribute('href', $element->getAttribute('longdesc'));
                $anchor->setAttribute('target', '_blank');
                $anchor->setAttribute($this->dataLongDescriptionForImage, $id);
                $anchor->setAttribute('class', $this->classLongDescriptionLink);
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
