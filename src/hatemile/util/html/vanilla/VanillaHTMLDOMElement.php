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

namespace hatemile\util\html\vanilla;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'HTMLDOMElement.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'HTMLDOMNode.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'VanillaHTMLDOMNode.php'
));

use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMNode;
use \hatemile\util\html\vanilla\VanillaHTMLDOMNode;

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

    public function getChildren()
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

    public function hasChildren()
    {
        $children = $this->element->childNodes;
        foreach ($children as $child) {
            if ($child instanceof \DOMElement) {
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
                $result = $this->element;
            }
        }
        if ($result != null) {
            return new VanillaHTMLDOMElement($result);
        }
        return null;
    }
}
