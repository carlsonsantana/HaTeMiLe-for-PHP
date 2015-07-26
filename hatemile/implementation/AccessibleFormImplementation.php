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

require_once dirname(__FILE__) . '/../AccessibleForm.php';
require_once dirname(__FILE__) . '/../util/HTMLDOMElement.php';
require_once dirname(__FILE__) . '/../util/HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../util/CommonFunctions.php';
require_once dirname(__FILE__) . '/../util/Configure.php';

use \hatemile\AccessibleForm;
use \hatemile\util\HTMLDOMElement;
use \hatemile\util\HTMLDOMParser;
use \hatemile\util\CommonFunctions;
use \hatemile\util\Configure;

/**
 * The AccessibleFormImplementation class is official implementation of
 * AccessibleForm interface.
 */
class AccessibleFormImplementation implements AccessibleForm {
	
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
	 * The description prefix of required fields.
	 * @var string
	 */
	protected $prefixRequiredField;
	
	/**
	 * The description suffix of required fields.
	 * @var string
	 */
	protected $suffixRequiredField;
	
	/**
	 * The description prefix of range fields for minimum value.
	 * @var string
	 */
	protected $prefixRangeMinField;
	
	/**
	 * The description suffix of range fields for minimum value.
	 * @var string
	 */
	protected $suffixRangeMinField;
	
	/**
	 * The description prefix of range fields for maximum value.
	 * @var string
	 */
	protected $prefixRangeMaxField;
	
	/**
	 * The description suffix of range fields for maximum value.
	 * @var string
	 */
	protected $suffixRangeMaxField;
	
	/**
	 * The description prefix of autocomplete fields.
	 * @var string
	 */
	protected $prefixAutoCompleteField;
	
	/**
	 * The description suffix of autocomplete fields.
	 * @var string
	 */
	protected $suffixAutoCompleteField;
	
	/**
	 * The value for description of field, when it has inline and list
	 * autocomplete.
	 * @var string
	 */
	protected $textAutoCompleteValueBoth;
	
	/**
	 * The value for description of field, when it has list autocomplete.
	 * @var string
	 */
	protected $textAutoCompleteValueList;
	
	/**
	 * The value for description of field, when it has inline autocomplete.
	 * @var string
	 */
	protected $textAutoCompleteValueInline;
	
	/**
	 * The value for description of field, when it not has autocomplete.
	 * @var string
	 */
	protected $textAutoCompleteValueNone;
	
	/**
	 * The name of attribute for not modify the elements.
	 * @var string
	 */
	protected $dataIgnore;
	
	/**
	 * The name of attribute that store the description prefix of required
	 * fields.
	 * @var string
	 */
	protected $dataLabelPrefixRequiredField;
	
	/**
	 * The name of attribute that store the description suffix of required
	 * fields.
	 * @var string
	 */
	protected $dataLabelSuffixRequiredField;
	
	/**
	 * The name of attribute that store the description prefix of range fields
	 * for minimum value.
	 * @var string
	 */
	protected $dataLabelPrefixRangeMinField;
	
	/**
	 * The name of attribute that store the description suffix of range fields
	 * for minimum value.
	 * @var string
	 */
	protected $dataLabelSuffixRangeMinField;
	
	/**
	 * The name of attribute that store the description prefix of range fields
	 * for maximum value.
	 * @var string
	 */
	protected $dataLabelPrefixRangeMaxField;
	
	/**
	 * The name of attribute that store the description suffix of range fields
	 * for maximum value.
	 * @var string
	 */
	protected $dataLabelSuffixRangeMaxField;
	
	/**
	 * The name of attribute that store the description prefix of autocomplete
	 * fields.
	 * @var string
	 */
	protected $dataLabelPrefixAutoCompleteField;
	
	/**
	 * The name of attribute that store the description suffix of autocomplete
	 * fields.
	 * @var string
	 */
	protected $dataLabelSuffixAutoCompleteField;
	
	/**
	 * Initializes a new object that manipulate the accessibility of the forms
	 * of parser.
	 * @param \hatemile\util\HTMLDOMParser $parser The HTML parser.
	 * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
	 */
	public function __construct(HTMLDOMParser $parser, Configure $configure) {
		$this->parser = $parser;
		$this->dataLabelPrefixRequiredField = 'data-prefixrequiredfield';
		$this->dataLabelSuffixRequiredField = 'data-suffixrequiredfield';
		$this->dataLabelPrefixRangeMinField = 'data-prefixvalueminfield';
		$this->dataLabelSuffixRangeMinField = 'data-suffixvalueminfield';
		$this->dataLabelPrefixRangeMaxField = 'data-prefixvaluemaxfield';
		$this->dataLabelSuffixRangeMaxField = 'data-suffixvaluemaxfield';
		$this->dataLabelPrefixAutoCompleteField = 'data-prefixautocompletefield';
		$this->dataLabelSuffixAutoCompleteField = 'data-suffixautocompletefield';
		$this->dataIgnore = 'data-ignoreaccessibilityfix';
		$this->prefixId = $configure->getParameter('prefix-generated-ids');
		$this->prefixRequiredField = $configure->getParameter('prefix-required-field');
		$this->suffixRequiredField = $configure->getParameter('suffix-required-field');
		$this->prefixRangeMinField = $configure->getParameter('prefix-range-min-field');
		$this->suffixRangeMinField = $configure->getParameter('suffix-range-min-field');
		$this->prefixRangeMaxField = $configure->getParameter('prefix-range-max-field');
		$this->suffixRangeMaxField = $configure->getParameter('suffix-range-max-field');
		$this->prefixAutoCompleteField = $configure->getParameter('prefix-autocomplete-field');
		$this->suffixAutoCompleteField = $configure->getParameter('suffix-autocomplete-field');
		$this->textAutoCompleteValueBoth = $configure->getParameter('text-autocomplete-value-both');
		$this->textAutoCompleteValueList = $configure->getParameter('text-autocomplete-value-list');
		$this->textAutoCompleteValueInline = $configure->getParameter('text-autocomplete-value-inline');
		$this->textAutoCompleteValueNone = $configure->getParameter('text-autocomplete-value-none');
	}
	
	/**
	 * Display in label the information of field.
	 * @param \hatemile\util\HTMLDOMElement $label The label.
	 * @param \hatemile\util\HTMLDOMElement $field The field.
	 * @param string $prefix The prefix.
	 * @param string $suffix The suffix.
	 * @param string $dataPrefix The name of prefix attribute.
	 * @param string $dataSuffix The name of suffix attribute.
	 */
	protected function addPrefixSuffix(HTMLDOMElement $label
			, HTMLDOMElement $field, $prefix, $suffix, $dataPrefix
			, $dataSuffix) {
		$content = $field->getAttribute('aria-label');
		if (!empty($prefix)) {
			$label->setAttribute($dataPrefix, $prefix);
			if (strpos($content, $prefix) === false) {
				$content = $prefix . ' ' . $content;
			}
		}
		if (!empty($suffix)) {
			$label->setAttribute($dataSuffix, $suffix);
			if (strpos($content, $suffix) === false) {
				$content .= ' ' . $suffix;
			}
		}
		$field->setAttribute('aria-label', $content);
	}
	
	/**
	 * Display in label the information if the field is required.
	 * @param \hatemile\util\HTMLDOMElement $label The label.
	 * @param \hatemile\util\HTMLDOMElement $requiredField The required field.
	 */
	protected function fixLabelRequiredField(HTMLDOMElement $label, HTMLDOMElement $requiredField) {
		if ((($requiredField->hasAttribute('required'))
				|| (($requiredField->hasAttribute('aria-required'))
				&& (strtolower($requiredField->getAttribute('aria-required')) === 'true')))
				&& ($requiredField->hasAttribute('aria-label'))
				&& (!$label->hasAttribute($this->dataLabelPrefixRequiredField))
				&& (!$label->hasAttribute($this->dataLabelSuffixRequiredField))) {
			$this->addPrefixSuffix($label, $requiredField, $this->prefixRequiredField
					, $this->suffixRequiredField, $this->dataLabelPrefixRequiredField
					, $this->dataLabelSuffixRequiredField);
		}
	}
	
	/**
	 * Display in label the information of range of field.
	 * @param \hatemile\util\HTMLDOMElement $label The label.
	 * @param \hatemile\util\HTMLDOMElement $rangeField The range field.
	 */
	protected function fixLabelRangeField(HTMLDOMElement $label, HTMLDOMElement $rangeField) {
		if ($rangeField->hasAttribute('aria-label')) {
			if (($rangeField->hasAttribute('min') || $rangeField->hasAttribute('aria-valuemin'))
					&& (!$label->hasAttribute($this->dataLabelPrefixRangeMinField))
					&& (!$label->hasAttribute($this->dataLabelSuffixRangeMinField))) {
				if ($rangeField->hasAttribute('min')) {
					$value = $rangeField->getAttribute('min');
				} else {
					$value = $rangeField->getAttribute('aria-valuemin');
				}
				$this->addPrefixSuffix($label, $rangeField
						, str_replace('{{value}}', $value, $this->prefixRangeMinField)
						, str_replace('{{value}}', $value, $this->suffixRangeMinField)
						, $this->dataLabelPrefixRangeMinField, $this->dataLabelSuffixRangeMinField);
			}
			if (($rangeField->hasAttribute('max') || $rangeField->hasAttribute('aria-valuemax'))
					&& (!$label->hasAttribute($this->dataLabelPrefixRangeMaxField))
					&& (!$label->hasAttribute($this->dataLabelSuffixRangeMaxField))) {
				if ($rangeField->hasAttribute('max')) {
					$value = $rangeField->getAttribute('max');
				} else {
					$value = $rangeField->getAttribute('aria-valuemax');
				}
				$this->addPrefixSuffix($label, $rangeField
						, str_replace('{{value}}', $value, $this->prefixRangeMaxField)
						, str_replace('{{value}}', $value, $this->suffixRangeMaxField)
						, $this->dataLabelPrefixRangeMaxField, $this->dataLabelSuffixRangeMaxField);
			}
		}
	}
	
	/**
	 * Display in label the information if the field has autocomplete.
	 * @param \hatemile\util\HTMLDOMElement $label The label.
	 * @param \hatemile\util\HTMLDOMElement $autoCompleteField The autocomplete field.
	 */
	protected function fixLabelAutoCompleteField(HTMLDOMElement $label
			, HTMLDOMElement $autoCompleteField) {
		$prefixAutoCompleteFieldModified = '';
		$suffixAutoCompleteFieldModified = '';
		if (($autoCompleteField->hasAttribute('aria-label'))
				&& (!$label->hasAttribute($this->dataLabelPrefixAutoCompleteField))
				&& (!$label->hasAttribute($this->dataLabelSuffixAutoCompleteField))) {
			$ariaAutocomplete = $this->getARIAAutoComplete($autoCompleteField);
			if (!empty($ariaAutocomplete)) {
				if ($ariaAutocomplete === 'both') {
					if (!empty($this->prefixAutoCompleteField)) {
						$prefixAutoCompleteFieldModified = str_replace('{{value}}'
								, $this->textAutoCompleteValueBoth, $this->prefixAutoCompleteField);
					}
					if (!empty($this->suffixAutoCompleteField)) {
						$suffixAutoCompleteFieldModified = str_replace('{{value}}'
								, $this->textAutoCompleteValueBoth, $this->suffixAutoCompleteField);
					}
				} else if ($ariaAutocomplete === 'none') {
					if (!empty($this->prefixAutoCompleteField)) {
						$prefixAutoCompleteFieldModified = str_replace('{{value}}'
								, $this->textAutoCompleteValueNone, $this->prefixAutoCompleteField);
					}
					if (!empty($this->suffixAutoCompleteField)) {
						$suffixAutoCompleteFieldModified = str_replace('{{value}}'
								, $this->textAutoCompleteValueNone, $this->suffixAutoCompleteField);
					}
				} else if ($ariaAutocomplete === 'list') {
					if (!empty($this->prefixAutoCompleteField)) {
						$prefixAutoCompleteFieldModified = str_replace('{{value}}'
								, $this->textAutoCompleteValueList, $this->prefixAutoCompleteField);
					}
					if (!empty($this->suffixAutoCompleteField)) {
						$suffixAutoCompleteFieldModified = str_replace('{{value}}'
								, $this->textAutoCompleteValueList, $this->suffixAutoCompleteField);
					}
				}
				$this->addPrefixSuffix($label, $autoCompleteField, $prefixAutoCompleteFieldModified
						, $suffixAutoCompleteFieldModified, $this->dataLabelPrefixAutoCompleteField
						, $this->dataLabelSuffixAutoCompleteField);
			}
		}
	}
	
	/**
	 * Returns the appropriate value for attribute aria-autocomplete of field.
	 * @param \hatemile\util\HTMLDOMElement $field The field.
	 * @return string The ARIA value of field.
	 */
	protected function getARIAAutoComplete(HTMLDOMElement $field) {
		$tagName = $field->getTagName();
		$type = null;
		if ($field->hasAttribute('type')) {
			$type = strtolower($field->getAttribute('type'));
		}
		if (($tagName === 'TEXTAREA') || (($tagName === 'INPUT')
				&& (!(('button' === $type) || ('submit' === $type)
					|| ('reset' === $type) || ('image' === $type)
					|| ('file' === $type) || ('checkbox' === $type)
					|| ('radio' === $type) || ('hidden' === $type))))) {
			$value = null;
			if ($field->hasAttribute('autocomplete')) {
				$value = strtolower($field->getAttribute('autocomplete'));
			} else {
				$form = $this->parser->find($field)->findAncestors('form')->firstResult();
				if (($form === null) && ($field->hasAttribute('form'))) {
					$form = $this->parser->find('#' . $field->getAttribute('form'))->firstResult();
				}
				if (($form !== null) && ($form->hasAttribute('autocomplete'))) {
					$value = strtolower($form->getAttribute('autocomplete'));
				}
			}
			if ('on' === $value) {
				return 'both';
			} else if (($field->hasAttribute('list')) && ($this->parser
					->find('datalist[id="' . $field->getAttribute('list') . '"]')->firstResult() !== null)) {
				return 'list';
			} else if ('off' === $value) {
				return 'none';
			}
		}
		return null;
	}
	
	/**
	 * Returns the labels of field.
	 * @param \hatemile\util\HTMLDOMElement $field The field.
	 * @return \hatemile\util\HTMLDOMElement[] The labels of field.
	 */
	protected function getLabels(HTMLDOMElement $field) {
		$labels = null;
		if ($field->hasAttribute('id')) {
			$labels = $this->parser->find('label[for="' . $field->getAttribute('id') . '"]')
					->listResults();
		}
		if (empty($labels)) {
			$labels = $this->parser->find($field)->findAncestors('label')->listResults();
		}
		return $labels;
	}
	
	public function fixRequiredField(HTMLDOMElement $requiredField) {
		if ($requiredField->hasAttribute('required')) {
			$requiredField->setAttribute('aria-required', 'true');
			
			$labels = $this->getLabels($requiredField);
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
		$labels = $this->getLabels($rangeField);
		foreach ($labels as $label) {
			$this->fixLabelRangeField($label, $rangeField);
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
	
	public function fixAutoCompleteField(HTMLDOMElement $autoCompleteField) {
		$ariaAutoComplete = $this->getARIAAutoComplete($autoCompleteField);
		if (!empty($ariaAutoComplete)) {
			$autoCompleteField->setAttribute('aria-autocomplete', $ariaAutoComplete);
			
			$labels = $this->getLabels($autoCompleteField);
			foreach ($labels as $label) {
				$this->fixLabelAutoCompleteField($label, $autoCompleteField);
			}
		}
	}
	
	public function fixAutoCompleteFields() {
		$elements = $this->parser
				->find('input[autocomplete],textarea[autocomplete],form[autocomplete] input,form[autocomplete] textarea,[list],[form]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixAutoCompleteField($element);
			}
		}
	}
	
	public function fixLabel(HTMLDOMElement $label) {
		if ($label->getTagName() === 'LABEL') {
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
				$this->fixLabelRangeField($label, $field);
				$this->fixLabelAutoCompleteField($label, $field);
				
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
}