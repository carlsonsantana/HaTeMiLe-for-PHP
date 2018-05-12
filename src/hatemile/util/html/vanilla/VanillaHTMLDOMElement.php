<?php
/**
 * Class VanillaHTMLDOMElement.
 * 
 * @package hatemile\util\html\vanilla
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\html\vanilla;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'HTMLDOMElement.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'VanillaHTMLDOMNode.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'VanillaHTMLDOMTextNode.php'
));

use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\vanilla\VanillaHTMLDOMNode;
use \hatemile\util\html\vanilla\VanillaHTMLDOMTextNode;

/**
 * The VanillaHTMLDOMElement class is official implementation of HTMLDOMElement
 * interface for the DOMElement.
 */
class VanillaHTMLDOMElement extends VanillaHTMLDOMNode implements HTMLDOMElement
{

    /**
     * The DOMElement native element encapsulated.
     * @var \DOMElement
     */
    protected $element;

    /**
     * Initializes a new object that encapsulate the DOMElement.
     * @param \DOMElement $element The DOMElement.
     */
    public function __construct(\DOMElement $element)
    {
        parent::__construct($element);
        $this->element = $element;
    }

    public function getTagName()
    {
        return strtoupper($this->element->tagName);
    }

    public function getAttribute($name)
    {
        return $this->element->getAttribute($name);
    }

    public function setAttribute($name, $value)
    {
        $this->element->setAttribute($name, $value);
    }

    public function removeAttribute($name)
    {
        if ($this->hasAttribute($name)) {
            $this->element->removeAttribute($name);
        }
    }

    public function hasAttribute($name)
    {
        return $this->element->hasAttribute($name);
    }

    public function hasAttributes()
    {
        return $this->element->hasAttributes();
    }

    public function getTextContent()
    {
        return $this->element->textContent;
    }

    public function appendElement(HTMLDOMElement $element)
    {
        $this->element->appendChild($element->getData());
        return $this;
    }

    public function prependElement(HTMLDOMElement $element)
    {
        $children = $this->element->childNodes;
        if (empty($children)) {
            $this->appendElement($element);
        } else {
            $this->element->insertBefore(
                $element->getData(),
                $children[0]
            );
        }
        return $this;
    }

    public function getChildrenElements()
    {
        $children = $this->element->childNodes;
        $elements = array();
        foreach ($children as $child) {
            if ($child instanceof \DOMElement) {
                array_push($elements, new VanillaHTMLDOMElement($child));
            }
        }
        return $elements;
    }

    public function getChildren()
    {
        $children = $this->element->childNodes;
        $nodes = array();
        foreach ($children as $child) {
            if ($child instanceof \DOMElement) {
                array_push($nodes, new VanillaHTMLDOMElement($child));
            } elseif ($child instanceof \DOMText) {
                array_push($nodes, new VanillaHTMLDOMTextNode($child));
            }
        }
        return $nodes;
    }

    public function appendText($text)
    {
        $this->element->appendChild(
            $this->element->ownerDocument->createTextNode($text)
        );
        return $this;
    }

    public function prependText($text)
    {
        $children = $this->element->childNodes;
        if (empty($children)) {
            $this->appendText($text);
        } else {
            $this->element->insertBefore(
                $this->element->ownerDocument->createTextNode($text),
                $children[0]
            );
        }
        return $this;
    }

    public function normalize()
    {
        if ($this->hasChildren()) {
            $last = null;
            $children = $this->getChildren();
            foreach ($children as $child) {
                if ($child instanceof VanillaHTMLDOMElement) {
                    $child->normalize();
                } elseif (
                    ($child instanceof VanillaHTMLDOMTextNode)
                    && ($last instanceof VanillaHTMLDOMTextNode)
                ) {
                    $child->prependText($last->getTextContent());
                    $last->removeNode();
                }

                $last = $child;
            }
        }
    }

    public function hasChildrenElements()
    {
        $children = $this->element->childNodes;
        foreach ($children as $child) {
            if ($child instanceof \DOMElement) {
                return true;
            }
        }
        return false;
    }

    public function hasChildren()
    {
        $children = $this->element->childNodes;
        foreach ($children as $child) {
            if (
                ($child instanceof \DOMElement)
                || ($child instanceof \DOMText)
            ) {
                return true;
            }
        }
        return false;
    }

    public function getInnerHTML()
    {
        $innerHTML = '';
        $children = $this->element->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML($child);
        }
        return $innerHTML;
    }

    public function getOuterHTML()
    {
        return $this->element->ownerDocument->saveXML($this->element);
    }

    public function cloneElement()
    {
        return new VanillaHTMLDOMElement($this->element->cloneNode(true));
    }

    public function getFirstElementChild()
    {
        $children = $this->element->childNodes;
        foreach ($children as $child) {
            if ($child instanceof \DOMElement) {
                return new VanillaHTMLDOMElement($child);
            }
        }
        return null;
    }

    public function getLastElementChild()
    {
        $children = $this->element->childNodes;
        foreach ($children as $child) {
            if ($child instanceof \DOMElement) {
                $result = $child;
            }
        }
        if ($result !== null) {
            return new VanillaHTMLDOMElement($result);
        }
        return null;
    }

    public function getFirstNodeChild()
    {
        $children = $this->element->childNodes;
        foreach ($children as $child) {
            if ($child instanceof \DOMElement) {
                return new VanillaHTMLDOMElement($child);
            } elseif ($child instanceof \DOMText) {
                return new VanillaHTMLDOMTextNode($child);
            }
        }
        return null;
    }

    public function getLastNodeChild()
    {
        $children = $this->element->childNodes;
        foreach ($children as $child) {
            if (
                ($child instanceof \DOMElement)
                || ($child instanceof \DOMText)
            ) {
                $result = $child;
            }
        }
        if ($result !== null) {
            if ($result instanceof \DOMElement) {
                return new VanillaHTMLDOMElement($result);
            } elseif ($child instanceof \DOMText) {
                return new VanillaHTMLDOMTextNode($result);
            }
        }
        return null;
    }
}
