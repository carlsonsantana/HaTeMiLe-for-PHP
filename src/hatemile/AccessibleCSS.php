<?php
/**
 * Interface AccessibleCSS.
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
 * The AccessibleCSS interface improve accessibility of CSS.
 */
interface AccessibleCSS
{

    /**
     * Provide the CSS features of speaking and speech properties in element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    public function provideSpeakProperties(HTMLDOMElement $element);

    /**
     * Provide the CSS features of speaking and speech properties in all
     * elements of page.
     */
    public function provideAllSpeakProperties();
}
