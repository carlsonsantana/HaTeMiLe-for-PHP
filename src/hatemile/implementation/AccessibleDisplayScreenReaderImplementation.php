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
     * The name of attribute that links the description of shortcut of element.
     * @var string
     */
    const DATA_ATTRIBUTE_ACCESSKEY_OF = 'data-attributeaccesskeyof';

    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $parser;

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
        $this->shortcutPrefix = $this->getShortcutPrefix(
            $userAgent,
            $configure->getParameter('text-standart-shortcut-prefix')
        );
        $this->attributeAccesskeyBefore = $configure->getParameter(
            'attribute-accesskey-before'
        );
        $this->attributeAccesskeyAfter = $configure->getParameter(
            'attribute-accesskey-after'
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
                $element->getAttribute('accesskey')
            );
            foreach ($keys as $key) {
                $key = strtoupper($key);
                $selector = (
                    '[' .
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ATTRIBUTE_ACCESSKEY_OF .
                    '="' .
                    $key .
                    '"]'
                );

                $item = $this->parser->createElement('li');
                $item->setAttribute(
                    AccessibleDisplayScreenReaderImplementation
                            ::DATA_ATTRIBUTE_ACCESSKEY_OF,
                    $key
                );
                $item->appendText(
                    $this->shortcutPrefix .
                    ' + ' .
                    $key .
                    ': ' .
                    $description
                );
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
}
