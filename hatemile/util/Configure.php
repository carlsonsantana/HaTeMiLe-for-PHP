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

namespace hatemile\util;

require_once __DIR__ . '/SelectorChange.php';

class Configure {
	protected $parameters;
	protected $selectorChanges;

	public function __construct($fileName = null) {
		$this->parameters = array();
		$this->selectorChanges = array();
		if ($fileName == null) {
			$fileName = __DIR__ . '/../hatemile-configure.xml';
		}

		$file = new \DOMDocument();
		$file->load($fileName);
		$document = $file->documentElement;
		$childNodes = $document->childNodes;
		$nodeParameters = null;
		$nodeSelectorChanges = null;
		for ($i = 0, $length = $childNodes->length; $i < $length; $i++) {
			$child = $childNodes->item($i);
			if ($child instanceof \DOMElement) {
				if (strtoupper($child->tagName) == 'PARAMETERS') {
					$nodeParameters = $child->childNodes;
				} else if (strtoupper($child->tagName) == 'SELECTOR-CHANGES') {
					$nodeSelectorChanges = $child->childNodes;
				}
			}
		}

		if ($nodeParameters != null) {
			for ($i = 0, $length = $nodeParameters->length; $i < $length; $i++) {
				$parameter = $nodeParameters->item($i);
				if ($parameter instanceof \DOMElement) {
					if ((strtoupper($parameter->tagName) == 'PARAMETER') && ($parameter->hasAttribute('name'))) {
						$this->parameters[$parameter->getAttribute('name')] = $parameter->textContent;
					}
				}
			}
		}

		if ($nodeSelectorChanges != null) {
			for ($i = 0, $length = $nodeSelectorChanges->length; $i < $length; $i++) {
				$selector = $nodeSelectorChanges->item($i);
				if ($selector instanceof \DOMElement) {
					if ((strtoupper($selector->tagName) == 'SELECTOR-CHANGE') && ($selector->hasAttribute('selector')) && ($selector->hasAttribute('attribute')) && ($selector->hasAttribute('value-attribute'))) {
						array_push($this->selectorChanges, new SelectorChange($selector->getAttribute('selector'), $selector->getAttribute('attribute'), $selector->getAttribute('value-attribute')));
					}
				}
			}
		}
	}

	public function getParameter($parameter) {
		return $this->parameters[$parameter];
	}

	public function getSelectorChanges() {
		return $this->selectorChanges;
	}

}
