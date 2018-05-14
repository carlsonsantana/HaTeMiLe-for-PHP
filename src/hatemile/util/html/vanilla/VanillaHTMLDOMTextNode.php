<?php
/**
 * Class VanillaHTMLDOMTextNode.
 * 
 * @package hatemile\util\html\vanilla
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\html\vanilla;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'HTMLDOMTextNode.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'VanillaHTMLDOMTextNode.php'
));

use \hatemile\util\html\HTMLDOMTextNode;
use \hatemile\util\html\vanilla\VanillaHTMLDOMNode;

/**
 * The VanillaHTMLDOMTextNode class is official implementation of
 * HTMLDOMTextNode for the DOMText.
 */
class VanillaHTMLDOMTextNode extends VanillaHTMLDOMNode implements
    HTMLDOMTextNode
{

    /**
     * The vanilla TextNode encapsulated.
     * @var \DOMText
     */
    protected $textNode;

    /**
     * Initializes a new object that encapsulate the DOMText.
     * @param \DOMText $textNode The DOMText.
     */
    public function __construct(\DOMText $textNode)
    {
        parent::__construct($textNode);
        $this->textNode = $textNode;
    }

    public function getTextContent()
    {
        return $this->textNode->wholeText;
    }

    public function setTextContent($text)
    {
        $newTextNode = $this->textNode->ownerDocument->createTextNode($text);
        $parent = $this->getParentElement()->getData();
        $parent->replaceChild($newTextNode, $this->textNode);
        $this->setData($newTextNode);
    }

    public function appendText($text)
    {
        $this->setTextContent($this->getTextContent() . $text);
        return $this;
    }

    public function prependText($text)
    {
        $this->setTextContent($text . $this->getTextContent());
        return $this;
    }
}
