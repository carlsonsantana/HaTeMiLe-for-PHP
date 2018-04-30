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
     * The id of list element that contains the description of shortcuts.
     * @var string
     */
    const ID_CONTAINER_SHORTCUTS = 'container-shortcuts';

    /**
     * The id of text of description of container of shortcuts descriptions.
     * @var string
     */
    const ID_TEXT_SHORTCUTS = 'text-shortcuts';

    /**
     * The name of attribute that link the list item element with the shortcut.
     * @var string
     */
    const DATA_ACCESS_KEY = 'data-shortcutdescriptionfor';

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
    protected $shortcutPrefix;

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
        $this->textShortcuts = $configure->getParameter('text-shortcuts');
        $this->listShortcutsAdded = false;
        $this->listShortcuts = null;
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
            '#' .
            AccessibleDisplayScreenReaderImplementation::ID_CONTAINER_SHORTCUTS
        )->firstResult();
        $htmlList = null;
        if ($container === null) {
            $local = $this->parser->find('body')->firstResult();
            if ($local !== null) {
                $container = $this->parser->createElement('div');
                $container->setAttribute(
                    'id',
                    AccessibleDisplayScreenReaderImplementation
                            ::ID_CONTAINER_SHORTCUTS
                );

                $textContainer = $this->parser->createElement('span');
                $textContainer->setAttribute(
                    'id',
                    AccessibleDisplayScreenReaderImplementation
                            ::ID_TEXT_SHORTCUTS
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
                        AccessibleDisplayScreenReaderImplementation
                                ::DATA_ACCESS_KEY .
                        '="' .
                        $key .
                        '"]'
                    );
                    if ($this->parser->find(
                        $this->listShortcuts
                    )->findChildren($attr)->firstResult() === null) {
                        $item = $this->parser->createElement('li');
                        $item->setAttribute(
                            AccessibleDisplayScreenReaderImplementation
                                    ::DATA_ACCESS_KEY,
                            $key
                        );
                        $item->appendText(
                            $this->shortcutPrefix .
                            ' + ' .
                            $key .
                            ': ' .
                            $description
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
}
