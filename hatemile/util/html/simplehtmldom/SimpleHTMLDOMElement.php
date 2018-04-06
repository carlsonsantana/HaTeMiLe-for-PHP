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

namespace hatemile\util\html\simplehtmldom;

require_once dirname(__FILE__) . '/../HTMLDOMElement.php';
require_once dirname(__FILE__) . '/SimpleHTMLDOMParser.php';

use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\simplehtmldom\SimpleHTMLDOMParser;

/**
 * The SimpleHTMLDOMElement class is official implementation of HTMLDOMElement
 * interface for the Simple HTML DOM library.
 */
class SimpleHTMLDOMElement implements HTMLDOMElement
{

    /**
     * The Simple HTML DOM native element encapsulated.
     * @var \simple_html_dom_node
     */
    protected $element;

    protected $parser;

    /**
     * Initializes a new object that encapsulate the Simple HTML DOM Node.
     * @param \simple_html_dom_node $element The Simple HTML DOM Node.
     */
    public function __construct(\simple_html_dom_node $element, SimpleHTMLDOMParser $parser)
    {
        $this->element = $element;
        $this->parser = $parser;
    }

    public function getTagName()
    {
        return strtoupper($this->element->nodeName());
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
            unset($this->element->attr[$name]);
        }
    }

    public function hasAttribute($name)
    {
        return $this->element->hasAttribute($name);
    }

    public function hasAttributes()
    {
        $attributes = $this->element->getAllAttributes();
        return !empty($attributes);
    }

    public function getTextContent()
    {
        return $this->element->text();
    }

    public function insertBefore(HTMLDOMElement $newElement)
    {
        $parent = $this->getParentElement()->getData();
        $data = $newElement->getData();
        $data->parent = $parent;
        $array = array($data);
        $indexChildren = array_search($this->element, $parent->children);
        $indexNodes = array_search($this->element, $parent->nodes);
        array_splice($parent->children, $indexChildren, 0, $array);
        array_splice($parent->nodes, $indexNodes, 0, $array);
        return $newElement;
    }

    public function insertAfter(HTMLDOMElement $newElement)
    {
        $parent = $this->getParentElement()->getData();
        $data = $newElement->getData();
        $data->parent = $parent;
        $array = array($data);
        $indexChildren = array_search($this->element, $parent->children) + 1;
        $indexNodes = array_search($this->element, $parent->nodes) + 1;
        array_splice($parent->children, $indexChildren, 0, $array);
        array_splice($parent->nodes, $indexNodes, 0, $array);
        return $newElement;
    }

    public function removeElement()
    {
        $parent = $this->getParentElement()->getData();
        $indexChildren = array_search($this->element, $parent->children);
        $indexNodes = array_search($this->element, $parent->nodes);
        $this->element->parent = null;
        array_splice($parent->children, $indexChildren, 1);
        array_splice($parent->nodes, $indexNodes, 1);
    }

    public function replaceElement(HTMLDOMElement $newElement)
    {
        $parent = $this->getParentElement()->getData();
        $newElement->getData()->parent = $parent;
        $indexChildren = array_search($this->element, $parent->children);
        $indexNodes = array_search($this->element, $parent->nodes);
        $this->element->parent = null;
        $parent->children[$indexChildren] = $newElement->getData();
        $parent->nodes[$indexNodes] = $newElement->getData();
    }

    public function appendElement(HTMLDOMElement $element)
    {
        $this->element->appendChild($element->getData());
        return $element;
    }

    public function getChildren()
    {
        $children = $this->element->children();
        $elements = array();
        foreach ($children as $child) {
            array_push($elements, new SimpleHTMLDOMElement($child, $this->parser));
        }
        return $elements;
    }

    public function appendText($text)
    {
        $this->element->appendChild(str_get_html($text)->nodes[0]);
    }

    public function hasChildren()
    {
        $children = $this->element->children();
        return !empty($children);
    }

    public function getParentElement()
    {
        if (empty($this->element->parent)) {
            return null;
        }
        return new SimpleHTMLDOMElement($this->element->parent, $this->parser);
    }

    public function getInnerHTML()
    {
        return $this->element->innertext();
    }

    public function getOuterHTML()
    {
        return $this->element->outertext();
    }

    public function getData()
    {
        return $this->element;
    }

    public function setData($data)
    {
        $this->element = $data;
    }

    public function cloneElement()
    {
        return new SimpleHTMLDOMElement(str_get_html($this->getOuterHTML())->firstChild(), $this->parser);
    }

    public function getFirstElementChild()
    {
        if (!$this->hasChildren()) {
            return null;
        }
        return new SimpleHTMLDOMElement($this->element->first_child(), $this->parser);
    }

    public function getLastElementChild()
    {
        if (!$this->hasChildren()) {
            return null;
        }
        return new SimpleHTMLDOMElement($this->element->last_child(), $this->parser);
    }
}
