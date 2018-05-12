<?php
/**
 * Abstract class VanillaHTMLDOMNode.
 * 
 * @package hatemile\util\html\vanilla
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\html\vanilla;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'HTMLDOMNode.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'VanillaHTMLDOMElement.php'
));

use \hatemile\util\html\HTMLDOMNode;
use \hatemile\util\html\vanilla\VanillaHTMLDOMElement;

/**
 * The VanillaHTMLDOMNode class is official implementation of HTMLDOMNode
 * interface for the DOMNode.
 */
abstract class VanillaHTMLDOMNode implements HTMLDOMNode
{

    /**
     * The vanilla node encapsulated.
     * @var \DOMNode
     */
    protected $node;

    /**
     * Initializes a new object that encapsulate the vanilla node.
     * @param \DOMNode $node The vanilla node.
     */
    protected function __construct(\DOMNode $node)
    {
        $this->node = $node;
    }

    public function insertBefore(HTMLDOMNode $newNode)
    {
        $this->getParentElement()->getData()->insertBefore(
            $newNode->getData(),
            $this->node
        );
        return $this;
    }

    public function insertAfter(HTMLDOMNode $newNode)
    {
        $nativeParent = $this->getParentElement()->getData();
        $children = $nativeParent->childNodes;
        $found = false;
        $added = false;
        foreach ($children as $child) {
            if ($found) {
                $nativeParent->insertBefore(
                    $newNode->getData(),
                    $child
                );
                $added = true;
                break;
            } elseif ($child === $this->node) {
                $found = true;
            }
        }
        if (!$added) {
            $nativeParent->appendChild($newNode->getData());
        }
        return $this;
    }

    public function removeNode()
    {
        $this->getParentElement()->getData()->removeChild($this->node);
        return $this;
    }

    public function replaceNode(HTMLDOMNode $newNode)
    {
        $this->getParentElement()->getData()->replaceChild(
            $newNode->getData(),
            $this->node
        );
        return $this;
    }

    public function getParentElement()
    {
        if (
            (empty($this->node->parentNode))
            || (!($this->node->parentNode instanceof \DOMElement))
        ) {
            return null;
        }
        return new VanillaHTMLDOMElement($this->node->parentNode);
    }

    public function getData()
    {
        return $this->node;
    }

    public function setData($data)
    {
        $this->node = $data;
    }

    public function equals($obj)
    {
        if ($this === $obj) {
            return true;
        }
        if (($obj !== null) && ($obj instanceof VanillaHTMLDOMNode)) {
            return $this->getData() === $obj->getData();
        }
        return false;
    }
}
