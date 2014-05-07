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

namespace hatemile\implementation;

require_once __DIR__ . '/../AccessibleForm.php';
require_once __DIR__ . '/../util/HTMLDOMElement.php';
require_once __DIR__ . '/../util/HTMLDOMParser.php';
require_once __DIR__ . '/../util/CommonFunctions.php';
require_once __DIR__ . '/../util/Configure.php';

use hatemile\AccessibleForm;
use hatemile\util\HTMLDOMElement;
use hatemile\util\HTMLDOMParser;
use hatemile\util\CommonFunctions;
use hatemile\util\Configure;

class AccessibleFormImpl implements AccessibleForm {
	protected $parser;
	protected $prefixId;
	protected $classRequiredField;
	protected $sufixRequiredField;
	protected $dataIgnore;

	public function __construct(HTMLDOMParser $parser, Configure $configure) {
		$this->parser = $parser;
		$this->prefixId = $configure->getParameter('prefix-generated-ids');
		$this->classRequiredField = $configure->getParameter('class-required-field');
		$this->sufixRequiredField = $configure->getParameter('sufix-required-field');
		$this->dataIgnore = $configure->getParameter('data-ignore');
	}

	public function fixRequiredField(HTMLDOMElement $element) {
		if ($element->hasAttribute('required')) {
			$element->setAttribute('aria-required', 'true');
			$labels = null;
			if ($element->hasAttribute('id')) {
				$labels = $this->parser->find('label[for=' . $element->getAttribute('id') . ']')->listResults();
			}
			if (empty($labels)) {
				$labels = $this->parser->find($element)->findAncestors('label')->listResults();
			}
			foreach ($labels as $label) {
				$label->setAttribute('class', CommonFunctions::increaseInList($label->getAttribute('class'), $this->classRequiredField));
			}
		}
	}

	public function fixRequiredFields() {
		$elements = $this->parser->find('[required]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixRequiredField($element);
			}
		}
	}

	public function fixDisabledField(HTMLDOMElement $element) {
		if ($element->hasAttribute('disabled')) {
			$element->setAttribute('aria-disabled', 'true');
		}
	}

	public function fixDisabledFields() {
		$elements = $this->parser->find('[disabled]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixDisabledField($element);
			}
		}
	}
	
	public function fixReadOnlyField(HTMLDOMElement $element) {
		if ($element->hasAttribute('readonly')) {
			$element->setAttribute('aria-readonly', 'true');
		}
	}

	public function fixReadOnlyFields() {
		$elements = $this->parser->find('[readonly]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixReadOnlyField($element);
			}
		}
	}
	
	public function fixRangeField(HTMLDOMElement $element) {
		if ($element->hasAttribute('min')) {
			$element->setAttribute('aria-valuemin', \trim($element->getAttribute('min')));
		}
		if ($element->hasAttribute('max')) {
			$element->setAttribute('aria-valuemax', \trim($element->getAttribute('max')));
		}
	}

	public function fixRangeFields() {
		$elements = $this->parser->find('[min],[max]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixRangeField($element);
			}
		}
	}
	
	public function fixTextField(HTMLDOMElement $element) {
		if (($element->getTagName() == 'INPUT') && ($element->hasAttribute('type'))) {
			$type = \trim(strtolower($element->getAttribute('type')));
			if (($type == 'text') || ($type == 'search') || ($type == 'email')
					|| ($type == 'url') || ($type == 'tel') || ($type == 'number')) {
				$element->setAttribute('aria-multiline', 'false');
			}
		} else if ($element->getTagName() == 'TEXTAREA') {
			$element->setAttribute('aria-multiline', 'true');
		}
	}

	public function fixTextFields() {
		$elements = $this->parser->find('input[type=text],input[type=search],input[type=email],input[type=url],input[type=tel],input[type=number],textarea')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixTextField($element);
			}
		}
	}
	
	public function fixSelectField(HTMLDOMElement $element) {
		if ($element->getTagName() == 'SELECT') {
			if ($element->hasAttribute('multiple')) {
				$element->setAttribute('aria-multiselectable', 'true');
			} else {
				$element->setAttribute('aria-multiselectable', 'false');
			}
		}
	}

	public function fixSelectFields() {
		$elements = $this->parser->find('select')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixSelectField($element);
			}
		}
	}

	public function fixLabel(HTMLDOMElement $element) {
		if ($element->getTagName() == 'LABEL') {
			$input = null;
			if ($element->hasAttribute('for')) {
				$input = $this->parser->find('#' . $element->getAttribute('for'))->firstResult();
			} else {
				$input = $this->parser->find($element)->findDescendants('input,select,textarea')->firstResult();
				if ($input != null) {
					CommonFunctions::generateId($input, $this->prefixId);
					$element->setAttribute('for', $input->getAttribute('id'));
				}
			}
			if ($input != null) {
				if (!$input->hasAttribute('aria-label')) {
					$label = \trim(preg_replace('/[ \n\r\t]+/', ' ', $element->getTextContent()));
					if ($input->hasAttribute('aria-required')) {
						if ((\trim(strtolower($input->getAttribute('aria-required'))) == 'true') && (strpos($label, $this->sufixRequiredField) === false)) {
							$label .= ' ' . $this->sufixRequiredField;
						}
					}
					$input->setAttribute('aria-label', $label);
				}
				CommonFunctions::generateId($element, $this->prefixId);
				$input->setAttribute('aria-labelledby', CommonFunctions::increaseInList($input->getAttribute('aria-labelledby'), $element->getAttribute('id')));
			}
		}
	}

	public function fixLabels() {
		$elements = $this->parser->find('label')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixLabel($element);
			}
		}
	}
}