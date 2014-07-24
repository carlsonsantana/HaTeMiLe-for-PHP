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

require_once dirname(__FILE__) . '/../AccessibleForm.php';
require_once dirname(__FILE__) . '/../util/HTMLDOMElement.php';
require_once dirname(__FILE__) . '/../util/HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../util/CommonFunctions.php';
require_once dirname(__FILE__) . '/../util/Configure.php';

use hatemile\AccessibleForm;
use hatemile\util\HTMLDOMElement;
use hatemile\util\HTMLDOMParser;
use hatemile\util\CommonFunctions;
use hatemile\util\Configure;

/**
 * The AccessibleFormImpl class is official implementation of AccessibleForm
 * interface.
 * @version 2014-07-23
 */
class AccessibleFormImpl implements AccessibleForm {
	
	/**
	 * The HTML parser.
	 * @var \hatemile\util\HTMLDOMParser
	 */
	protected $parser;
	
	/**
	 * The prefix of generated id.
	 * @var string
	 */
	protected $prefixId;
	
	/**
	 * The name of attribute for the label of a required field.
	 * @var string
	 */
	protected $dataLabelRequiredField;
	
	/**
	 * The prefix of required field.
	 * @var string
	 */
	protected $prefixRequiredField;
	
	/**
	 * The suffix of required field.
	 * @var string
	 */
	protected $suffixRequiredField;
	
	/**
	 * The name of attribute for that the element not can be modified by
	 * HaTeMiLe.
	 * @var string
	 */
	protected $dataIgnore;
	
	/**
	 * Initializes a new object that manipulate the accessibility of the forms
	 * of parser.
	 * @param \hatemile\util\HTMLDOMParser $parser The HTML parser.
	 * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
	 */
	public function __construct(HTMLDOMParser $parser, Configure $configure) {
		$this->parser = $parser;
		$this->prefixId = $configure->getParameter('prefix-generated-ids');
		$this->dataLabelRequiredField = 'data-' . $configure->getParameter('data-label-required-field');
		$this->dataIgnore = 'data-' . $configure->getParameter('data-ignore');
		$this->prefixRequiredField = $configure->getParameter('prefix-required-field');
		$this->suffixRequiredField = $configure->getParameter('suffix-required-field');
	}
	
	/**
	 * Do the label or the aria-label to inform in label that the field is
	 * required.
	 * @param \hatemile\util\HTMLDOMElement $label The label.
	 * @param \hatemile\util\HTMLDOMElement $requiredField The required field.
	 */
	protected function fixLabelRequiredField(HTMLDOMElement $label, HTMLDOMElement $requiredField) {
		if (($requiredField->hasAttribute('required'))
				|| (($requiredField->hasAttribute('aria-required'))
				&& (strtolower($requiredField->getAttribute('aria-required')) === 'true'))) {
			if (!$label->hasAttribute($this->dataLabelRequiredField)) {
				$label->setAttribute($this->dataLabelRequiredField, 'true');
			}
			
			if ($requiredField->hasAttribute('aria-label')) {
				$contentLabel = $requiredField->getAttribute('aria-label');
				if ((!empty($this->prefixRequiredField))
						&& (strpos($contentLabel, $this->prefixRequiredField) === false)) {
					$contentLabel = $this->prefixRequiredField . ' ' . $contentLabel;
				}
				if ((!empty($this->suffixRequiredField))
						&& (strpos($contentLabel, $this->suffixRequiredField) === false)) {
					$contentLabel .= ' ' . $this->suffixRequiredField;
				}
				$requiredField->setAttribute('aria-label', $contentLabel);
			}
		}
	}
	
	/**
	 * Fix the control to inform if it has autocomplete and the type.
	 * @param \hatemile\util\HTMLDOMElement $control The form control.
	 * @param boolean $active If the element has autocomplete.
	 */
	protected function fixControlAutoComplete(HTMLDOMElement $control, $active) {
		if ($active) {
			$control->setAttribute('aria-autocomplete', 'both');
		} else if (!(($active === null) && ($control->hasAttribute('aria-autocomplete')))) {
			if ($control->hasAttribute('list')) {
				$list = $this->parser->find('datalist[id=' . $control->getAttribute('list') . ']')
						->firstResult();
				if ($list !== null) {
					$control->setAttribute('aria-autocomplete', 'list');
				}
			}
			if (($active === false) && ((!$control->hasAttribute('aria-autocomplete'))
					|| (!(strtolower($control->getAttribute('aria-autocomplete')) === 'list')))) {
				$control->setAttribute('aria-autocomplete', 'none');
			}
		}
	}
	
	public function fixRequiredField(HTMLDOMElement $requiredField) {
		if ($requiredField->hasAttribute('required')) {
			$requiredField->setAttribute('aria-required', 'true');
			
			$labels = null;
			if ($requiredField->hasAttribute('id')) {
				$labels = $this->parser
						->find('label[for=' . $requiredField->getAttribute('id') . ']')->listResults();
			}
			if (empty($labels)) {
				$labels = $this->parser->find($requiredField)->findAncestors('label')->listResults();
			}
			foreach ($labels as $label) {
				$this->fixLabelRequiredField($label, $requiredField);
			}
		}
	}
	
	public function fixRequiredFields() {
		$requiredFields = $this->parser->find('[required]')->listResults();
		foreach ($requiredFields as $requiredField) {
			if (!$requiredField->hasAttribute($this->dataIgnore)) {
				$this->fixRequiredField($requiredField);
			}
		}
	}
	
	public function fixRangeField(HTMLDOMElement $rangeField) {
		if ($rangeField->hasAttribute('min')) {
			$rangeField->setAttribute('aria-valuemin', $rangeField->getAttribute('min'));
		}
		if ($rangeField->hasAttribute('max')) {
			$rangeField->setAttribute('aria-valuemax', $rangeField->getAttribute('max'));
		}
	}
	
	public function fixRangeFields() {
		$rangeFields = $this->parser->find('[min],[max]')->listResults();
		foreach ($rangeFields as $rangeField) {
			if (!$rangeField->hasAttribute($this->dataIgnore)) {
				$this->fixRangeField($rangeField);
			}
		}
	}
	
	public function fixLabel(HTMLDOMElement $label) {
		if ($label->getTagName() === 'LABEL') {
			$field = null;
			if ($label->hasAttribute('for')) {
				$field = $this->parser->find('#' . $label->getAttribute('for'))->firstResult();
			} else {
				$field = $this->parser->find($label)
						->findDescendants('input,select,textarea')->firstResult();
				
				if ($field !== null) {
					CommonFunctions::generateId($field, $this->prefixId);
					$label->setAttribute('for', $field->getAttribute('id'));
				}
			}
			if ($field !== null) {
				if (!$field->hasAttribute('aria-label')) {
					$field->setAttribute('aria-label'
							, \trim(preg_replace('/[ \n\r\t]+/', ' ', $label->getTextContent())));
				}
				
				$this->fixLabelRequiredField($label, $field);
				
				CommonFunctions::generateId($label, $this->prefixId);
				$field->setAttribute('aria-labelledby', CommonFunctions::increaseInList
						($field->getAttribute('aria-labelledby') , $label->getAttribute('id')));
			}
		}
	}
	
	public function fixLabels() {
		$labels = $this->parser->find('label')->listResults();
		foreach ($labels as $label) {
			if (!$label->hasAttribute($this->dataIgnore)) {
				$this->fixLabel($label);
			}
		}
	}
	
	public function fixAutoComplete(HTMLDOMElement $element) {
		if ($element->hasAttribute('autocomplete')) {
			$active = null;
			$value = $element->getAttribute('autocomplete');
			if ($value === 'on') {
				$active = true;
			} else if ($value === 'off') {
				$active = false;
			}
			if ($active !== null) {
				if ($element->getTagName() === 'FORM') {
					$controls = $this->parser->find($element)->findDescendants('input,textarea')
							->listResults();
					if ($element->hasAttribute('id')) {
						$id = $element->getAttribute('id');
						$controls = array_merge($controls,  $this->parser
								->find('input[form=' . $id . '],textarea[form=' . $id . ']')
								->listResults());
					}
					foreach ($controls as $control) {
						$fix = true;
						if (($control->getTagName() === 'INPUT') && ($control->hasAttribute("type"))) {
							$type = strtolower($control->getAttribute('type'));
							if (($type === 'button') || ($type === 'submit') || ($type === 'reset')
									|| ($type === 'image') || ($type === 'file')
									|| ($type === 'checkbox') || ($type === 'radio')
									|| ($type === 'password') || ($type === 'hidden')) {
								$fix = false;
							}
						}
						if ($fix) {
							$autoCompleteControlFormValue = $control->getAttribute('autocomplete');
							if ($autoCompleteControlFormValue === 'on') {
								$this->fixControlAutoComplete($control, true);
							} else if ($autoCompleteControlFormValue === 'off') {
								$this->fixControlAutoComplete($control, false);
							} else {
								$this->fixControlAutoComplete($control, $active);
							}
						}
					}
				} else {
					$this->fixControlAutoComplete($element, $active);
				}
			}
		}
		if ((!$element->hasAttribute('aria-autocomplete')) && ($element->hasAttribute('list'))) {
			$this->fixControlAutoComplete($element, null);
		}
	}
	
	public function fixAutoCompletes() {
		$elements = $this->parser->find('[autocomplete],[list]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixAutoComplete($element);
			}
		}
	}
}