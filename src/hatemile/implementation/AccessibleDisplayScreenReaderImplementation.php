<?php
/**
 * Class AccessibleDisplayScreenReaderImplementation.
 * 
 * @package hatemile\implementation
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\implementation;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'AccessibleDisplay.php'
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

use \hatemile\AccessibleDisplay;
use \hatemile\util\CommonFunctions;
use \hatemile\util\Configure;
use \hatemile\util\IDGenerator;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;

/**
 * The AccessibleDisplayScreenReaderImplementation class is official
 * implementation of AccessibleDisplay for screen readers.
 */
class AccessibleDisplayScreenReaderImplementation implements AccessibleDisplay
{

    /**
     * The id of list element that contains the description of shortcuts, before
     * the whole content of page.
     * @var string
     */
    const ID_CONTAINER_SHORTCUTS_BEFORE = 'container-shortcuts-before';

    /**
     * The id of list element that contains the description of shortcuts, after
     * the whole content of page.
     * @var string
     */
    const ID_CONTAINER_SHORTCUTS_AFTER = 'container-shortcuts-after';

    /**
     * The HTML class of text of description of container of shortcuts
     * descriptions.
     * @var string
     */
    const CLASS_TEXT_SHORTCUTS = 'text-shortcuts';

    /**
     * The HTML class of content to force the screen reader show the current
     * state of element, before it.
     * @var string
     */
    const CLASS_FORCE_READ_BEFORE = 'force-read-before';

    /**
     * The HTML class of content to force the screen reader show the current
     * state of element, after it.
     * @var string
     */
    const CLASS_FORCE_READ_AFTER = 'force-read-after';

    /**
     * The name of attribute that links the description of shortcut of element.
     * @var string
     */
    const DATA_ATTRIBUTE_ACCESSKEY_OF = 'data-attributeaccesskeyof';

    /**
     * The name of attribute that links the content of role of element with the
     * element.
     * @var string
     */
    const DATA_ROLE_OF = 'data-roleof';

    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $parser;

    /**
     * The configuration of HaTeMiLe.
     * @var \hatemile\util\Configure
     */
    protected $configure;

    /**
     * The id generator.
     * @var \hatemile\util\IDGenerator
     */
    protected $idGenerator;

    /**
     * The browser shortcut prefix.
     * @var string
     */
    protected $shortcutPrefix;

    /**
     * The description of shortcut list, before all elements.
     * @var string
     */
    protected $attributeAccesskeyBefore;

    /**
     * The description of shortcut list, after all elements.
     * @var string
     */
    protected $attributeAccesskeyAfter;

    /**
     * The prefix description of shortcut list, before all elements.
     * @var string
     */
    protected $attributeAccesskeyPrefixBefore;

    /**
     * The suffix description of shortcut list, before all elements.
     * @var string
     */
    protected $attributeAccesskeySuffixBefore;

    /**
     * The prefix description of shortcut list, after all elements.
     * @var string
     */
    protected $attributeAccesskeyPrefixAfter;

    /**
     * The suffix description of shortcut list, after all elements.
     * @var string
     */
    protected $attributeAccesskeySuffixAfter;

    /**
     * The prefix text of role of element, before it.
     * @var string
     */
    protected $attributeRolePrefixBefore;

    /**
     * The suffix text of role of element, before it.
     * @var string
     */
    protected $attributeRoleSuffixBefore;

    /**
     * The prefix text of role of element, after it.
     * @var string
     */
    protected $attributeRolePrefixAfter;

    /**
     * The suffix text of role of element, after it.
     * @var string
     */
    protected $attributeRoleSuffixAfter;

    /**
     * The list element of shortcuts, before the whole content of page.
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $listShortcutsBefore;

    /**
     * The list element of shortcuts, after the whole content of page.
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $listShortcutsAfter;

    /**
     * The state that indicates if the list of shortcuts of page was added.
     * @var bool
     */
    protected $listShortcutsAdded;

    /**
     * Initializes a new object that manipulate the display for screen readers
     * of parser.
     * @param \hatemile\util\html\HTMLDOMParser $parser The HTML parser.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     * @param string $userAgent The user agent of browser.
     */
    public function __construct(
        HTMLDOMParser $parser,
        Configure $configure,
        $userAgent = null
    ) {
        $this->parser = $parser;
        $this->configure = $configure;
        $this->idGenerator = new IDGenerator('display');
        $this->shortcutPrefix = $this->getShortcutPrefix(
            $userAgent,
            $configure->getParameter('attribute-accesskey-default')
        );
        $this->attributeAccesskeyBefore = $configure->getParameter(
            'attribute-accesskey-before'
        );
        $this->attributeAccesskeyAfter = $configure->getParameter(
            'attribute-accesskey-after'
        );
        $this->attributeAccesskeyPrefixBefore = $configure->getParameter(
            'attribute-accesskey-prefix-before'
        );
        $this->attributeAccesskeySuffixBefore = $configure->getParameter(
            'attribute-accesskey-suffix-before'
        );
        $this->attributeAccesskeyPrefixAfter = $configure->getParameter(
            'attribute-accesskey-prefix-after'
        );
        $this->attributeAccesskeySuffixAfter = $configure->getParameter(
            'attribute-accesskey-suffix-after'
        );
        $this->attributeRolePrefixBefore = $configure->getParameter(
            'attribute-role-prefix-before'
        );
        $this->attributeRoleSuffixBefore = $configure->getParameter(
            'attribute-role-suffix-before'
        );
        $this->attributeRolePrefixAfter = $configure->getParameter(
            'attribute-role-prefix-after'
        );
        $this->attributeRoleSuffixAfter = $configure->getParameter(
            'attribute-role-suffix-after'
        );
        $this->listShortcutsAdded = false;
        $this->listShortcutsBefore = null;
        $this->listShortcutsAfter = null;
    }

    /**
     * Returns the shortcut prefix of browser.
     * @param string $userAgent The user agent of browser.
     * @param string $standartPrefix The default prefix.
     * @return string The shortcut prefix of browser.
     */
    protected function getShortcutPrefix($userAgent, $standartPrefix)
    {
        if ($userAgent !== null) {
            $userAgent = strtolower($userAgent);
            $opera = strpos($userAgent, 'opera') !== false;
            $mac = strpos($userAgent, 'mac') !== false;
            $konqueror = strpos($userAgent, 'konqueror') !== false;
            $spoofer = strpos($userAgent, 'spoofer') !== false;
            $safari = strpos($userAgent, 'applewebkit') !== false;
            $windows = strpos($userAgent, 'windows') !== false;
            $chrome = strpos($userAgent, 'chrome') !== false;
            $firefox = (
                strpos($userAgent, 'firefox') !== false
                || strpos($userAgent, 'minefield') !== false
            );
            $ie = (
                (strpos($userAgent, 'msie') !== false)
                || (strpos($userAgent, 'trident') !== false)
            );

            if ($opera) {
                return 'SHIFT + ESC';
            } elseif ($chrome && $mac && !$spoofer) {
                return 'CTRL + OPTION';
            } elseif ($safari && !$windows && !$spoofer) {
                return 'CTRL + ALT';
            } elseif (!$windows && ($safari || $mac || $konqueror)) {
                return 'CTRL';
            } elseif ($firefox) {
                return 'ALT + SHIFT';
            } elseif ($chrome || $ie) {
                return 'ALT';
            } else {
                return $standartPrefix;
            }
        } else {
            return $standartPrefix;
        }
    }

    /**
     * Returns the description of role.
     * @param string role The role.
     * @return string The description of role.
     */
    protected function getRoleDescription($role) {
        $parameter = 'role-' . strtolower($role);
        if ($this->configure->hasParameter($parameter)) {
            return $this->configure->getParameter($parameter);
        } else {
            return null;
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
                    '#' .
                    $descriptionId
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
        return \trim(\preg_replace('/[ \n\r\t]+/', ' ', $description));
    }

    /**
     * Generate the list of shortcuts of page.
     */
    protected function generateListShortcuts()
    {
        $local = $this->parser->find('body')->firstResult();
        if ($local !== null) {
            $containerBefore = $this->parser->find(
                '#' .
                AccessibleDisplayScreenReaderImplementation
                    ::ID_CONTAINER_SHORTCUTS_BEFORE
            )->firstResult();
            if (
                ($containerBefore === null)
                && (!empty($this->attributeAccesskeyBefore))
            ) {
                $containerBefore = $this->parser->createElement('div');
                $containerBefore->setAttribute(
                    'id',
                    AccessibleDisplayScreenReaderImplementation
                            ::ID_CONTAINER_SHORTCUTS_BEFORE
                );

                $textContainer = $this->parser->createElement('span');
                $textContainer->setAttribute(
                    'class',
                    AccessibleDisplayScreenReaderImplementation
                            ::CLASS_TEXT_SHORTCUTS
                );
                $textContainer->appendText($this->attributeAccesskeyBefore);

                $containerBefore->appendElement($textContainer);
                $local->prependElement($containerBefore);
            }
            if ($containerBefore !== null) {
                $this->listShortcutsBefore = $this->parser->find(
                    $containerBefore
                )->findChildren('ul')->firstResult();
                if ($this->listShortcutsBefore === null) {
                    $this->listShortcutsBefore = $this->parser->createElement(
                        'ul'
                    );
                    $containerBefore->appendElement($this->listShortcutsBefore);
                }
            }


            $containerAfter = $this->parser->find(
                '#' .
                AccessibleDisplayScreenReaderImplementation
                    ::ID_CONTAINER_SHORTCUTS_AFTER
            )->firstResult();
            if (
                ($containerAfter === null)
                && (!empty($this->attributeAccesskeyAfter))
            ) {
                $containerAfter = $this->parser->createElement('div');
                $containerAfter->setAttribute(
                    'id',
                    AccessibleDisplayScreenReaderImplementation
                            ::ID_CONTAINER_SHORTCUTS_AFTER
                );

                $textContainer = $this->parser->createElement('span');
                $textContainer->setAttribute(
                    'class',
                    AccessibleDisplayScreenReaderImplementation
                            ::CLASS_TEXT_SHORTCUTS
                );
                $textContainer->appendText($this->attributeAccesskeyAfter);

                $containerAfter->appendElement($textContainer);
                $local->appendElement($containerAfter);
            }
            if ($containerAfter !== null) {
                $this->listShortcutsAfter = $this->parser->find(
                    $containerAfter
                )->findChildren('ul')->firstResult();
                if ($this->listShortcutsAfter === null) {
                    $this->listShortcutsAfter = $this->parser->createElement(
                        'ul'
                    );
                    $containerAfter->appendElement($this->listShortcutsAfter);
                }
            }
        }
        $this->listShortcutsAdded = true;
    }

    /**
     * Insert a element before or after other element.
     * @param \hatemile\util\html\HTMLDOMElement $element The reference element.
     * @param \hatemile\util\html\HTMLDOMElement $insertedElement The element
     * that be inserted.
     * @param bool $before To insert the element before the other element.
     */
    protected function insert(
        HTMLDOMElement $element,
        HTMLDOMElement $insertedElement,
        $before
    ) {
        $tagName = $element->getTagName();
        $appendTags = array(
            'BODY',
            'A',
            'FIGCAPTION',
            'LI',
            'DT',
            'DD',
            'LABEL',
            'OPTION',
            'TD',
            'TH'
        );
        $controls = array('INPUT', 'SELECT', 'TEXTAREA');
        if ($tagName === 'HTML') {
            $body = $this->parser->find('body')->firstResult();
            if ($body !== null) {
                $this->insert($body, $insertedElement, $before);
            }
        } elseif (in_array($tagName, $appendTags)) {
            if ($before) {
                $element->prependElement($insertedElement);
            } else {
                $element->appendElement($insertedElement);
            }
        } elseif (in_array($tagName, $controls)) {
            $labels = array();
            if ($element->hasAttribute('id')) {
                $labels = $this->parser->find(
                    'label[for="' .
                    $element->getAttribute('id') .
                    '"]'
                )->listResults();
            }
            if (empty($labels)) {
                $labels = $this->parser->find($element)->findAncestors(
                    'label'
                )->listResults();
            }
            foreach ($labels as $label) {
                $this->insert($label, $insertedElement, $before);
            }
        } elseif ($before) {
            $element->insertBefore($insertedElement);
        } else {
            $element->insertAfter($insertedElement);
        }
    }

    /**
     * Force the screen reader display an information of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The reference element.
     * @param string $textBefore The text content to show before the element.
     * @param string $textAfter The text content to show after the element.
     * @param string $dataOf The name of attribute that links the content with
     * element.
     */
    protected function forceReadSimple(
        HTMLDOMElement $element,
        $textBefore,
        $textAfter,
        $dataOf
    ) {
        $this->idGenerator->generateId($element);
        $identifier = $element->getAttribute('id');
        $selector = '[' . $dataOf . '="' . $identifier . '"]';

        $referenceBefore = $this->parser->find(
            '.' .
            AccessibleDisplayScreenReaderImplementation
                    ::CLASS_FORCE_READ_BEFORE .
            $selector
        )->firstResult();
        $referenceAfter = $this->parser->find(
            '.' .
            AccessibleDisplayScreenReaderImplementation
                    ::CLASS_FORCE_READ_AFTER .
            $selector
        )->firstResult();
        $references = $this->parser->find($selector)->listResults();
        for ($i = sizeof($references) - 1; $i >= 0; $i--) {
            if (
                ($references[$i]->equals($referenceBefore))
                || ($references[$i]->equals($referenceAfter))
            ) {
                array_splice($references, $i, 1);
            }
        }

        if (empty($references)) {
            if (!empty($textBefore)) {
                if ($referenceBefore !== null) {
                    $referenceBefore->removeNode();
                }

                $span = $this->parser->createElement('span');
                $span->setAttribute(
                    'class',
                    AccessibleDisplayScreenReaderImplementation
                            ::CLASS_FORCE_READ_BEFORE
                );
                $span->setAttribute($dataOf, $identifier);
                $span->appendText($textBefore);
                $this->insert($element, $span, true);
            }
            if (!empty($textAfter)) {
                if ($referenceAfter !== null) {
                    $referenceAfter->removeNode();
                }

                $span = $this->parser->createElement('span');
                $span->setAttribute(
                    'class',
                    AccessibleDisplayScreenReaderImplementation
                            ::CLASS_FORCE_READ_AFTER
                );
                $span->setAttribute($dataOf, $identifier);
                $span->appendText($textAfter);
                $this->insert($element, $span, false);
            }
        }
    }

    /**
     * Force the screen reader display an information of element with prefixes
     * or suffixes.
     * @param \hatemile\util\html\HTMLDOMElement $element The reference element.
     * @param string $value The value to be show.
     * @param string $textPrefixBefore The prefix of value to show before the
     * element.
     * @param string $textSuffixBefore The suffix of value to show before the
     * element.
     * @param string $textPrefixAfter The prefix of value to show after the
     * element.
     * @param string $textSuffixAfter The suffix of value to show after the
     * element.
     * @param string $dataOf The name of attribute that links the content with
     * element.
     */
    protected function forceRead(
        HTMLDOMElement $element,
        $value,
        $textPrefixBefore,
        $textSuffixBefore,
        $textPrefixAfter,
        $textSuffixAfter,
        $dataOf
    ) {
        $textBefore = '';
        $textAfter = '';
        if ((!empty($textPrefixBefore)) || (!empty($textSuffixBefore))) {
            $textBefore = $textPrefixBefore . $value . $textSuffixBefore;
        }
        if ((!empty($textPrefixAfter)) || (!empty($textSuffixAfter))) {
            $textAfter = $textPrefixAfter . $value . $textSuffixAfter;
        }
        $this->forceReadSimple($element, $textBefore, $textAfter, $dataOf);
    }

    public function displayShortcut(HTMLDOMElement $element)
    {
        if ($element->hasAttribute('accesskey')) {
            $description = $this->getDescription($element);
            if (!$element->hasAttribute('title')) {
                $element->setAttribute('title', $description);
            }

            if (!$this->listShortcutsAdded) {
                $this->generateListShortcuts();
            }

            $keys = preg_split(
                '/[ \n\t\r]+/',
                strtoupper($element->getAttribute('accesskey'))
            );
            foreach ($keys as $key) {
                $selector = (
                    '[' .
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ATTRIBUTE_ACCESSKEY_OF .
                    '="' .
                    $key .
                    '"]'
                );
                $shortcut = $this->shortcutPrefix . ' + ' . $key;
                $this->forceRead(
                    $element,
                    $shortcut,
                    $this->attributeAccesskeyPrefixBefore,
                    $this->attributeAccesskeySuffixBefore,
                    $this->attributeAccesskeyPrefixAfter,
                    $this->attributeAccesskeySuffixAfter,
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ATTRIBUTE_ACCESSKEY_OF
                );

                $item = $this->parser->createElement('li');
                $item->setAttribute(
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ATTRIBUTE_ACCESSKEY_OF,
                    $key
                );
                $item->appendText($shortcut . ': ' . $description);
                if (
                    ($this->listShortcutsBefore)
                    && ($this->parser->find(
                        $this->listShortcutsBefore
                    )->findChildren($selector)->firstResult() === null)
                ) {
                    $this->listShortcutsBefore->appendElement(
                        $item->cloneElement()
                    );
                }
                if (
                    ($this->listShortcutsAfter)
                    && ($this->parser->find(
                        $this->listShortcutsAfter
                    )->findChildren($selector)->firstResult() === null)
                ) {
                    $this->listShortcutsAfter->appendElement(
                        $item->cloneElement()
                    );
                }
            }
        }
    }

    public function displayAllShortcuts()
    {
        $elements = $this->parser->find('[accesskey]')->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->displayShortcut($element);
            }
        }
    }

    public function displayRole(HTMLDOMElement $element)
    {
        if ($element->hasAttribute('role')) {
            $roleDescription = $this->getRoleDescription(
                $element->getAttribute('role')
            );
            if ($roleDescription !== null) {
                $this->forceRead(
                    $element,
                    $roleDescription,
                    $this->attributeRolePrefixBefore,
                    $this->attributeRoleSuffixBefore,
                    $this->attributeRolePrefixAfter,
                    $this->attributeRoleSuffixAfter,
                    AccessibleDisplayScreenReaderImplementation::DATA_ROLE_OF
                );
            }
        }
    }

    public function displayAllRoles()
    {
        $elements = $this->parser->find('[role]')->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->displayRole($element);
            }
        }
    }
}
