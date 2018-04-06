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

require_once dirname(__FILE__) . '/../HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../../CommonFunctions.php';
require_once dirname(__FILE__) . '/../../Configure.php';
require_once dirname(__FILE__) . '/SimpleHTMLDOMElement.php';

use \hatemile\util\html\HTMLDOMParser;
use \hatemile\util\CommonFunctions;
use \hatemile\util\html\simplehtmldom\SimpleHTMLDOMElement;
use \hatemile\util\Configure;

/**
 * The class SimpleHTMLDOMParser is official implementation of HTMLDOMParser
 * interface for the Simple HTML DOM library.
 */
class SimpleHTMLDOMParser implements HTMLDOMParser
{
    
    /**
     * The root element of the parser.
     * @var \simple_html_dom
     */
    protected $document;
    
    /**
     * The found elements.
     * @var \simple_html_dom_node
     */
    protected $results;
    
    /**
     * The prefix of generated id.
     * @var string
     */
    protected $prefixId;
    
    /**
     * Initializes a new object that encapsulate the parser of Simple HTML DOM
     * library.
     * @param string|\simple_html_dom $codeOrParser The html code of page or the
     * parser from Simple HTML DOM library.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     */
    public function __construct($codeOrParser, Configure $configure)
    {
        if (is_string($codeOrParser)) {
            $this->document = str_get_html($codeOrParser, true, true, DEFAULT_TARGET_CHARSET, false);
        } else if ($codeOrParser instanceof \simple_html_dom) {
            $this->document = $codeOrParser;
        }
        $this->prefixId = $configure->getParameter('prefix-generated-ids');
    }
    
    protected function getSelectorOfElement($selector)
    {
        if ($selector instanceof SimpleHTMLDOMElement) {
            $autoid = false;
            if (!$selector->hasAttribute('id')) {
                CommonFunctions::generateId($selector, $this->prefixId);
                $autoid = true;
            }
            return array('selector' => '#' . $selector->getAttribute('id'), 'autoid' => $autoid);
        } else {
            return array('selector' => $selector, 'autoid' => false);
        }
    }
    
    public function find($selector)
    {
        if ($selector instanceof SimpleHTMLDOMElement) {
            $this->results = array($selector->getData());
        } else {
            $this->results = $this->document->find($selector);
        }
        return $this;
    }
    
    public function findChildren($selector)
    {
        $sel = $this->getSelectorOfElement($selector);
        $results = $this->results;
        $this->results = array();
        foreach ($results as $result) {
            $elements = $result->find($sel['selector']);
            foreach ($elements as $element) {
                if ($element->parent == $result) {
                    array_push($this->results, $element);
                }
            }
        }
        if ($sel['autoid']) {
            $selector->removeAttribute('id');
        }
        return $this;
    }
    
    public function findDescendants($selector)
    {
        $sel = $this->getSelectorOfElement($selector);
        $results = $this->results;
        $this->results = array();
        foreach ($results as $result) {
            $this->results = array_merge($this->results, $result->find($sel['selector']));
        }
        if ($sel['autoid']) {
            $selector->removeAttribute('id');
        }
        return $this;
    }
    
    public function findAncestors($selector)
    {
        $sel = $this->getSelectorOfElement($selector);
        $selectorChildren = array();
        foreach ($this->results as $result) {
            $selChildren = $this->getSelectorOfElement(new SimpleHTMLDOMElement($result, $this));
            array_push($selectorChildren, $selChildren);
        }
        $parents = $this->document->find($sel['selector']);
        $this->results = array();
        foreach ($parents as $parent) {
            foreach ($selectorChildren as $selectorChild) {
                $result = $parent->find($selectorChild['selector']);
                if (!empty($result)) {
                    array_push($this->results, $parent);
                    break;
                }
            }
        }
        foreach ($selectorChildren as $selectorChild) {
            if ($selectorChild['autoid']) {
                $this->find($selectorChild['selector'])->firstResult()->removeAttribute('id');
            }
        }
        if ($sel['autoid']) {
            $selector->removeAttribute('id');
        }
        return $this;
    }
    
    public function firstResult()
    {
        if (empty($this->results)) {
            return null;
        }
        return new SimpleHTMLDOMElement($this->results[0], $this);
    }
    
    public function lastResult()
    {
        if (empty($this->results)) {
            return null;
        }
        return new SimpleHTMLDOMElement($this->results[sizeof($this->results) - 1], $this);
    }
    
    public function listResults()
    {
        $array = array();
        foreach ($this->results as $item) {
            array_push($array, new SimpleHTMLDOMElement($item, $this));
        }
        return $array;
    }
    
    public function createElement($tag)
    {
        return new SimpleHTMLDOMElement(str_get_html('<' . $tag . '></' . $tag . '>')
                ->firstChild(), $this);
    }
    
    public function getHTML()
    {
        return $this->document->save();
    }
    
    public function getParser()
    {
        return $this->document;
    }
    
    public function clearParser()
    {
        $this->document->clear();
        unset($this->document);
        unset($this->prefixId);
        unset($this->results);
    }
}
