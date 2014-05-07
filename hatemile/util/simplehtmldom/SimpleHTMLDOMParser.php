<?php
/*
Copyright 2014 Carlson Santana Cruz

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

namespace hatemile\util\simplehtmldom;

require_once __DIR__ . '/../HTMLDOMParser.php';
require_once __DIR__ . '/../CommonFunctions.php';
require_once __DIR__ . '/../Configure.php';
require_once __DIR__ . '/SimpleHTMLDOMElement.php';

use hatemile\util\HTMLDOMParser;
use hatemile\util\CommonFunctions;
use hatemile\util\simplehtmldom\SimpleHTMLDOMElement;
use hatemile\util\Configure;

class SimpleHTMLDOMParser implements HTMLDOMParser {
	protected $document;
	protected $results;
	protected $prefixId;
	
	protected function getSelectorOfElement($selector) {
		if ($selector instanceof SimpleHTMLDOMElement) {
			CommonFunctions::generateId($selector, $this->prefixId);
			return '#' . $selector->getAttribute('id');
		} else {
			return $selector;
		}
	}

	public function __construct($code, Configure $configure) {
		$this->document = str_get_html($code, true, true, DEFAULT_TARGET_CHARSET, false);
		$this->prefixId = $configure->getParameter('prefix-generated-ids');
	}
	
	public function find($selector) {
		if ($selector instanceof SimpleHTMLDOMElement) {
			$this->results = array($selector->getData());
		} else {
			$this->results = $this->document->find($selector);
		}
		return $this;
	}

	public function findChildren($selector) {
		$selector = $this->getSelectorOfElement($selector);
		$results = $this->results;
		$this->results = array();
		foreach ($results as $result) {
			$elements = $result->find($selector);
			foreach ($elements as $element) {
				if ($element->parent == $result) {
					array_push($this->results, $element);
				}
			}
		}
		return $this;
	}
	
	public function findDescendants($selector) {
		$selector = $this->getSelectorOfElement($selector);
		$results = $this->results;
		$this->results = array();
		foreach ($results as $result) {
			$this->results = array_merge($this->results, $result->find($selector));
		}
		return $this;
	}
	
	public function findAncestors($selector) {
		$selector = $this->getSelectorOfElement($selector);
		$selectorChildren = array();
		foreach ($this->results as $result) {
			$sel = $this->getSelectorOfElement(new SimpleHTMLDOMElement($result));
			array_push($selectorChildren, $sel);
		}
		$parents = $this->document->find($selector);
		$this->results = array();
		foreach ($parents as $parent) {
			foreach ($selectorChildren as $selectorChild) {
				$result = $parent->find($selectorChild);
				if (!empty($result)) {
					array_push($this->results, $parent);
					break;
				}
			}
		}
		return $this;
	}
	
	public function firstResult() {
		if (empty($this->results)) {
			return null;
		}
		return new SimpleHTMLDOMElement($this->results[0]);
	}
	
	public function lastResult() {
		if (empty($this->results)) {
			return null;
		}
		return new SimpleHTMLDOMElement($this->results[sizeof($this->results) - 1]);
	}

	public function listResults() {
		$array = array();
		foreach ($this->results as $item) {
			array_push($array, new SimpleHTMLDOMElement($item));
		}
		return $array;
	}
	
	public function createElement($tag) {
		return new SimpleHTMLDOMElement(str_get_html('<' . $tag . '></' . $tag . '>')->firstChild());
	}

	public function getHTML() {
		return $this->document->save();
	}
	
	public function clearParser() {
		$this->document->clear();
		unset($this->document);
		unset($this->prefixId);
		unset($this->results);
	}
}