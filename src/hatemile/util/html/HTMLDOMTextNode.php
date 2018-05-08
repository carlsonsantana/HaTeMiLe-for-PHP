<?php
/**
 * Interface HTMLDOMTextNode.
 * 
 * @package hatemile\util\html
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\html;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'HTMLDOMNode.php'
));

use \hatemile\util\html\HTMLDOMNode;

/**
 * The HTMLDOMTextNode interface contains the methods for access of the HTML
 * TextNode.
 */
interface HTMLDOMTextNode extends HTMLDOMNode
{

    /**
     * Change the text content of text node.
     * @param string $text The new text content.
     */
    public function setTextContent($text);
}
