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

namespace hatemile\implementation;

require_once dirname(__FILE__) . '/../AccessibleSelector.php';
require_once dirname(__FILE__) . '/../util/HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../util/Configure.php';

use \hatemile\AccessibleSelector;
use \hatemile\util\HTMLDOMParser;
use \hatemile\util\Configure;

/**
 * The AccessibleSelectorImplementation class is official implementation of
 * AccessibleSelector interface.
 */
class AccessibleSelectorImplementation implements AccessibleSelector {
	
	/**
	 * The HTML parser.
	 * @var \hatemile\util\HTMLDOMParser
	 */
	protected $parser;
	
	/**
	 * The changes that will be done in selectors.
	 * @var \hatemile\util\SelectorChange
	 */
	protected $changes;
	
	/**
	 * The name of attribute for not modify the elements.
	 * @var string
	 */
	protected $dataIgnore;
	
	/**
	 * Initializes a new object that manipulate the accessibility through of the
	 * selectors of the configuration file.
	 * @param \hatemile\util\HTMLDOMParser $parser The HTML parser.
	 * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
	 */
	public function __construct(HTMLDOMParser $parser, Configure $configure) {
		$this->parser = $parser;
		$this->changes = $configure->getSelectorChanges();
		$this->dataIgnore = 'data-ignoreaccessibilityfix';
	}
	
	public function fixSelectors() {
		foreach ($this->changes as $change) {
			$elements = $this->parser->find($change->getSelector())->listResults();
			foreach ($elements as $element) {
				if (!$element->hasAttribute($this->dataIgnore)) {
					$element->setAttribute($change->getAttribute(), $change->getValueForAttribute());
				}
			}
		}
	}
}