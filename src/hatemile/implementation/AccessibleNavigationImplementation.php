<?php
/**
 * Class AccessibleNavigationImplementation.
 * 
 * @package hatemile\implementation
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
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
    'IDGenerator.php'
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
use \hatemile\util\IDGenerator;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;

/**
 * The AccessibleNavigationImplementation class is official implementation of
 * AccessibleNavigation.
 */
class AccessibleNavigationImplementation implements AccessibleNavigation
{

    /**
     * The id of list element that contains the skippers.
     * @var string
     */
    const ID_CONTAINER_SKIPPERS = 'container-skippers';

    /**
     * The id of list element that contains the links for the headings, before
     * the whole content of page.
     * @var string
     */
    const ID_CONTAINER_HEADING_BEFORE = 'container-heading-before';

    /**
     * The id of list element that contains the links for the headings, after
     * the whole content of page.
     * @var string
     */
    const ID_CONTAINER_HEADING_AFTER = 'container-heading-after';

    /**
     * The HTML class of text of description of container of heading links.
     * @var string
     */
    const CLASS_TEXT_HEADING = 'text-heading';

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
     * The HTML class of force link, before it.
     * @var string
     */
    const CLASS_FORCE_LINK_BEFORE = 'force-link-before';

    /**
     * The HTML class of force link, after it.
     * @var string
     */
    const CLASS_FORCE_LINK_AFTER = 'force-link-after';

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
    const DATA_ATTRIBUTE_LONG_DESCRIPTION_OF =
            'data-attributelongdescriptionof';

    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $parser;

    /**
     * The id generator.
     * @var \hatemile\util\IDGenerator
     */
    protected $idGenerator;

    /**
     * The text of description of container of heading links, before all
     * elements.
     * @var string
     */
    protected $elementsHeadingBefore;

    /**
     * The text of description of container of heading links, after all
     * elements.
     * @var string
     */
    protected $elementsHeadingAfter;

    /**
     * The prefix of content of long description, before the image.
     * @var string
     */
    protected $attributeLongDescriptionPrefixBefore;

    /**
     * The suffix of content of long description, before the image.
     * @var string
     */
    protected $attributeLongDescriptionSuffixBefore;

    /**
     * The prefix of content of long description, after the image.
     * @var string
     */
    protected $attributeLongDescriptionPrefixAfter;

    /**
     * The suffix of content of long description, after the image.
     * @var string
     */
    protected $attributeLongDescriptionSuffixAfter;

    /**
     * The skippers configured.
     * @var string[][]
     */
    protected $skippers;

    /**
     * The state that indicates if the container of skippers has added.
     * @var bool
     */
    protected $listSkippersAdded;

    /**
     * The list element of skippers.
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $listSkippers;

    /**
     * The list element of table of content, before the whole content of page;
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $listHeadingBefore;

    /**
     * The list element of table of content, after the whole content of page;
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $listHeadingAfter;

    /**
     * The state that indicates if the sintatic heading of parser be validated.
     * @var bool
     */
    protected $validateHeading;

    /**
     * The state that indicates if the sintatic heading of parser is correct.
     * @var bool
     */
    protected $validHeading;

    /**
     * The state that indicates if the container of table of content has added.
     * @var bool
     */
    protected $listHeadingAdded;

    /**
     * Initializes a new object that manipulate the accessibility of the
     * navigation of parser.
     * @param \hatemile\util\html\HTMLDOMParser $parser The HTML parser.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     * @param string $skipperFileName The file path of skippers configuration.
     */
    public function __construct(
        HTMLDOMParser $parser,
        Configure $configure,
        $skipperFileName = null
    ) {
        $this->parser = $parser;
        $this->idGenerator = new IDGenerator('navigation');
        $this->elementsHeadingBefore = $configure->getParameter(
            'elements-heading-before'
        );
        $this->elementsHeadingAfter = $configure->getParameter(
            'elements-heading-after'
        );
        $this->attributeLongDescriptionPrefixBefore = $configure->getParameter(
            'attribute-longdescription-prefix-before'
        );
        $this->attributeLongDescriptionSuffixBefore = $configure->getParameter(
            'attribute-longdescription-suffix-before'
        );
        $this->attributeLongDescriptionPrefixAfter = $configure->getParameter(
            'attribute-longdescription-prefix-after'
        );
        $this->attributeLongDescriptionSuffixAfter = $configure->getParameter(
            'attribute-longdescription-suffix-after'
        );
        $this->skippers = $this->getSkippers($skipperFileName, $configure);
        $this->listSkippersAdded = false;
        $this->listHeadingAdded = false;
        $this->validateHeading = false;
        $this->validHeading = false;
        $this->listSkippers = null;
        $this->listHeadingBefore = null;
        $this->listHeadingAfter = null;
    }

    /**
     * Returns the skippers of configuration.
     * @param string $fileName The file path of skippers configuration.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     * @return string[][] The skippers of configuration.
     */
    protected function getSkippers($fileName, $configure)
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
                        'description' => $configure->getParameter(
                            $child->getAttribute('description')
                        ),
                        'shortcut' => $child->getAttribute('shortcut')
                    )
                );
            }
        }

        return $skippers;
    }

    /**
     * Generate the list of skippers of page.
     * @return \hatemile\util\html\HTMLDOMElement The list of skippers of page.
     */
    protected function generateListSkippers()
    {
        $container = $this->parser->find(
            '#' .
            AccessibleNavigationImplementation::ID_CONTAINER_SKIPPERS
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
                $local->prependElement($container);
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
        $this->listSkippersAdded = true;
        return $htmlList;
    }

    /**
     * Generate the list of heading links of page.
     */
    protected function generateListHeading()
    {
        $local = $this->parser->find('body')->firstResult();
        if ($local !== null) {
            $containerBefore = $this->parser->find(
                '#' .
                AccessibleNavigationImplementation::ID_CONTAINER_HEADING_BEFORE
            )->firstResult();
            if (
                ($containerBefore === null)
                && (!empty($this->elementsHeadingBefore))
            ) {
                $containerBefore = $this->parser->createElement('div');
                $containerBefore->setAttribute(
                    'id',
                    AccessibleNavigationImplementation
                            ::ID_CONTAINER_HEADING_BEFORE
                );

                $textContainerBefore = $this->parser->createElement('span');
                $textContainerBefore->setAttribute(
                    'class',
                    AccessibleNavigationImplementation::CLASS_TEXT_HEADING
                );
                $textContainerBefore->appendText($this->elementsHeadingBefore);

                $containerBefore->appendElement($textContainerBefore);
                $local->prependElement($containerBefore);
            }

            if ($containerBefore !== null) {
                $this->listHeadingBefore = $this->parser->find(
                    $containerBefore
                )->findChildren('ol')->firstResult();
                if ($this->listHeadingBefore === null) {
                    $this->listHeadingBefore = $this->parser->createElement(
                        'ol'
                    );
                    $containerBefore->appendElement($this->listHeadingBefore);
                }
            }


            $containerAfter = $this->parser->find(
                '#' .
                AccessibleNavigationImplementation::ID_CONTAINER_HEADING_AFTER
            )->firstResult();
            if (
                ($containerAfter === null)
                && (!empty($this->elementsHeadingAfter))
            ) {
                $containerAfter = $this->parser->createElement('div');
                $containerAfter->setAttribute(
                    'id',
                    AccessibleNavigationImplementation
                            ::ID_CONTAINER_HEADING_AFTER
                );

                $textContainerAfter = $this->parser->createElement('span');
                $textContainerAfter->setAttribute(
                    'class',
                    AccessibleNavigationImplementation::CLASS_TEXT_HEADING
                );
                $textContainerAfter->appendText($this->elementsHeadingAfter);

                $containerAfter->appendElement($textContainerAfter);
                $local->appendElement($containerAfter);
            }

            if ($containerAfter !== null) {
                $this->listHeadingAfter = $this->parser->find(
                    $containerAfter
                )->findChildren('ol')->firstResult();
                if ($this->listHeadingAfter === null) {
                    $this->listHeadingAfter = $this->parser->createElement(
                        'ol'
                    );
                    $containerAfter->appendElement($this->listHeadingAfter);
                }
            }
        }
        $this->listHeadingAdded = true;
    }

    /**
     * Returns the level of heading.
     * @param \hatemile\util\html\HTMLDOMElement $element The heading.
     * @return int The level of heading.
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
     * Check that the headings of page are sintatic correct.
     * @return bool True if the headings of page are sintatic correct or false
     * if not.
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
        $this->idGenerator->generateId($element);
        $anchor = null;
        $at = '[' . $dataAttribute . '="' . $element->getAttribute('id') . '"]';
        if ($this->parser->find($at)->firstResult() === null) {
            if ($element->getTagName() === 'A') {
                $anchor = $element;
            } else {
                $anchor = $this->parser->createElement('a');
                $this->idGenerator->generateId($anchor);
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

    public function provideNavigationBySkipper(HTMLDOMElement $element)
    {
        $skipper = null;
        foreach ($this->skippers as $auxiliarSkipper) {
            $elements = $this->parser->find(
                $auxiliarSkipper['selector']
            )->listResults();
            foreach ($elements as $auxiliarElement) {
                if ($element->equals($auxiliarElement)) {
                    $skipper = $auxiliarSkipper;
                    break;
                }
            }
            if ($skipper !== null) {
                break;
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

                    $this->idGenerator->generateId($link);

                    $itemLink->appendElement($link);
                    $this->listSkippers->appendElement($itemLink);
                }
            }
        }
    }

    public function provideNavigationByAllSkippers()
    {
        foreach ($this->skippers as $skipper) {
            $elements = $this->parser->find(
                $skipper['selector']
            )->listResults();
            foreach ($elements as $element) {
                if (CommonFunctions::isValidElement($element)) {
                    $this->provideNavigationBySkipper($element);
                }
            }
        }
    }

    public function provideNavigationByHeading(HTMLDOMElement $heading)
    {
        if (!$this->validateHeading) {
            $this->validHeading = $this->isValidHeading();
        }
        if ($this->validHeading) {
            $anchor = $this->generateAnchorFor(
                $heading,
                AccessibleNavigationImplementation::DATA_HEADING_ANCHOR_FOR,
                AccessibleNavigationImplementation::CLASS_HEADING_ANCHOR
            );
            if ($anchor !== null) {
                if (!$this->listHeadingAdded) {
                    $this->generateListHeading();
                }
                $listBefore = null;
                $listAfter = null;
                $level = $this->getHeadingLevel($heading);
                if ($level === 1) {
                    $listBefore = $this->listHeadingBefore;
                    $listAfter = $this->listHeadingAfter;
                } else {
                    $selector = (
                        '[' .
                        AccessibleNavigationImplementation::DATA_HEADING_LEVEL .
                        '="' .
                        ((string) ($level - 1)) .
                        '"]'
                    );
                    if ($this->listHeadingBefore !== null) {
                        $superItemBefore = $this->parser->find(
                            $this->listHeadingBefore
                        )->findDescendants($selector)->lastResult();
                        if ($superItemBefore !== null) {
                            $listBefore = $this->parser->find(
                                $superItemBefore
                            )->findChildren('ol')->firstResult();
                            if ($listBefore === null) {
                                $listBefore = $this->parser->createElement(
                                    'ol'
                                );
                                $superItemBefore->appendElement($listBefore);
                            }
                        }
                    }
                    if ($this->listHeadingAfter !== null) {
                        $superItemAfter = $this->parser->find(
                            $this->listHeadingAfter
                        )->findDescendants($selector)->lastResult();
                        if ($superItemAfter !== null) {
                            $listAfter = $this->parser->find(
                                $superItemAfter
                            )->findChildren('ol')->firstResult();
                            if ($listAfter === null) {
                                $listAfter = $this->parser->createElement('ol');
                                $superItemAfter->appendElement($listAfter);
                            }
                        }
                    }
                }

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
                $link->appendText($heading->getTextContent());
                $item->appendElement($link);

                if ($listBefore !== null) {
                    $listBefore->appendElement($item->cloneElement());
                }
                if ($listAfter !== null) {
                    $listAfter->appendElement($item->cloneElement());
                }
            }
        }
    }

    public function provideNavigationByAllHeadings()
    {
        $headings = $this->parser->find('h1,h2,h3,h4,h5,h6')->listResults();
        foreach ($headings as $heading) {
            if (CommonFunctions::isValidElement($heading)) {
                $this->provideNavigationByHeading($heading);
            }
        }
    }

    public function provideNavigationToLongDescription(HTMLDOMElement $image)
    {
        if (
            ($image->hasAttribute('longdesc'))
            && ($image->hasAttribute('alt'))
        ) {
            $this->idGenerator->generateId($image);
            $id = $image->getAttribute('id');
            $selector = (
                '[' .
                AccessibleNavigationImplementation
                        ::DATA_ATTRIBUTE_LONG_DESCRIPTION_OF .
                '="' .
                $id .
                '"]'
            );
            $selectorBefore = (
                '.' .
                AccessibleNavigationImplementation::CLASS_FORCE_LINK_BEFORE .
                $selector
            );
            $selectorAfter = (
                '.' .
                AccessibleNavigationImplementation::CLASS_FORCE_LINK_AFTER .
                $selector
            );
            if (
                ($this->parser->find($selectorBefore)->firstResult() === null)
                && (!(
                    (empty($this->attributeLongDescriptionPrefixBefore))
                    || (empty($this->attributeLongDescriptionSuffixBefore))
                ))
            ) {
                $beforeText = \trim(
                    $this->attributeLongDescriptionPrefixBefore .
                    \trim($image->getAttribute('alt')) .
                    $this->attributeLongDescriptionSuffixBefore
                );
                $beforeAnchor = $this->parser->createElement('a');
                $beforeAnchor->setAttribute(
                    'href',
                    $image->getAttribute('longdesc')
                );
                $beforeAnchor->setAttribute('target', '_blank');
                $beforeAnchor->setAttribute(
                    AccessibleNavigationImplementation
                            ::DATA_ATTRIBUTE_LONG_DESCRIPTION_OF,
                    $id
                );
                $beforeAnchor->setAttribute(
                    'class',
                    AccessibleNavigationImplementation
                            ::CLASS_FORCE_LINK_BEFORE
                );
                $beforeAnchor->appendText($beforeText);
                $image->insertBefore($beforeAnchor);
            }
            if (
                ($this->parser->find($selectorAfter)->firstResult() === null)
                && (!(
                    (empty($this->attributeLongDescriptionPrefixAfter))
                    || (empty($this->attributeLongDescriptionSuffixAfter))
                ))
            ) {
                $afterText = \trim(
                    $this->attributeLongDescriptionPrefixAfter .
                    \trim($image->getAttribute('alt')) .
                    $this->attributeLongDescriptionSuffixAfter
                );
                $afterAnchor = $this->parser->createElement('a');
                $afterAnchor->setAttribute(
                    'href',
                    $image->getAttribute('longdesc')
                );
                $afterAnchor->setAttribute('target', '_blank');
                $afterAnchor->setAttribute(
                    AccessibleNavigationImplementation
                            ::DATA_ATTRIBUTE_LONG_DESCRIPTION_OF,
                    $id
                );
                $afterAnchor->setAttribute(
                    'class',
                    AccessibleNavigationImplementation
                            ::CLASS_FORCE_LINK_AFTER
                );
                $afterAnchor->appendText($afterText);
                $image->insertAfter($afterAnchor);
            }
        }
    }

    public function provideNavigationToAllLongDescriptions()
    {
        $images = $this->parser->find('[longdesc]')->listResults();
        foreach ($images as $image) {
            if (CommonFunctions::isValidElement($image)) {
                $this->provideNavigationToLongDescription($image);
            }
        }
    }
}
