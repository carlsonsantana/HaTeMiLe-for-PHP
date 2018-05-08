<?php
/**
 * Interface AccessibleDisplay.
 * 
 * @package hatemile
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'util',
    'html',
    'HTMLDOMElement.php'
));

use \hatemile\util\html\HTMLDOMElement;

/**
 * The AccessibleDisplay interface improve accessibility, showing informations.
 */
interface AccessibleDisplay
{

    /**
     * Display the shortcuts of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element with
     * shortcuts.
     */
    public function displayShortcut(HTMLDOMElement $element);

    /**
     * Display all shortcuts of page.
     */
    public function displayAllShortcuts();
}
