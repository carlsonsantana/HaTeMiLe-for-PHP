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

namespace hatemile\util\phpquery;

require_once dirname(__FILE__) . '/../HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../CommonFunctions.php';
require_once dirname(__FILE__) . '/../vanilla/VanillaHTMLDOMElement.php';

use \hatemile\util\HTMLDOMParser;
use hatemile\util\vanilla\VanillaHTMLDOMElement;

/**
 * The class phpQueryHTMLDOMParser is official implementation of HTMLDOMParser
 * interface for the phpQuery library.
 */
class phpQueryHTMLDOMParser implements HTMLDOMParser {
	
	/**
	 * The root element of the parser.
	 * @var \phpQueryObject
	 */
	protected $document;
	
	/**
	 * The found elements.
	 * @var \phpQueryObject
	 */
	protected $results;
	
	/**
	 * Initializes a new object that encapsulate the parser of phpQuery
	 * library.
	 * @param string|\phpQueryObject $codeOrParser The html code of page or the
	 * parser from phpQuery library.
	 * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
	 */
	public function __construct($codeOrParser) {
		if (is_string($codeOrParser)) {
			$this->document = \phpQuery::newDocumentHTML($codeOrParser);
		} else if ($codeOrParser instanceof \phpQueryObject) {
			$this->document = $codeOrParser;
		}
	}
	
	public function find($selector) {
		if ($selector instanceof VanillaHTMLDOMElement) {
			$this->results = \pq($selector->getData(), $this->document->getDocumentID());
		} else {
			$this->results = \pq($selector, $this->document->getDocumentID());
		}
		return $this;
	}
	
	public function findChildren($selector) {
		if ($selector instanceof VanillaHTMLDOMElement) {
			$this->results = $this->results->children($selector->getData());
		} else {
			$this->results = $this->results->children($selector);
		}
		
		return $this;
	}
	
	public function findDescendants($selector) {
		if ($selector instanceof VanillaHTMLDOMElement) {
			$this->results = $this->results->find($selector->getData());
		} else {
			$this->results = $this->results->find($selector);
		}
		
		return $this;
	}
	
	public function findAncestors($selector) {
		if ($selector instanceof VanillaHTMLDOMElement) {
			$this->results = $this->results->parents($selector->getData());
		} else {
			$this->results = $this->results->parents($selector);
		}
		
		return $this;
	}
	
	public function firstResult() {
		if (empty($this->results->elements)) {
			return null;
		}
		return new VanillaHTMLDOMElement($this->results->elements[0], $this);
	}
	
	public function lastResult() {
		if (empty($this->results->elements)) {
			return null;
		}
		return new VanillaHTMLDOMElement($this->results->elements[sizeof($this->results->elements) - 1], $this);
	}
	
	public function listResults() {
		$array = array();
		foreach ($this->results->elements as $item) {
			array_push($array, new VanillaHTMLDOMElement($item, $this));
		}
		return $array;
	}
	
	public function createElement($tag) {
		return new VanillaHTMLDOMElement($this->document->document->createElement($tag), $this);
	}
	
	public function getHTML() {
		return $this->document->htmlOuter();
	}
	
	public function getParser() {
		return $this->document;
	}
	
	public function clearParser() {
		pq('*')->remove();
		$this->document = null;
	}
}