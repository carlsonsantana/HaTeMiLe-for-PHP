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

namespace hatemile;

require_once dirname(__FILE__) . '/util/HTMLDOMElement.php';

use hatemile\util\HTMLDOMElement;

/**
 * The AccessibleForm interface fix the problems of accessibility associated
 * with the forms.
 * @version 2014-07-23
 */
interface AccessibleForm {
	
	/**
	 * Fix required field.
	 * @param \hatemile\util\HTMLDOMElement $requiredField The element that will
	 * be fixed.
	 * @see http://www.w3.org/TR/WCAG20-TECHS/H90.html H90: Indicating required form controls using label or legend
	 * @see http://www.w3.org/TR/2014/NOTE-WCAG20-TECHS-20140311/ARIA2 ARIA2: Identifying a required field with the aria-required property
	 * @see http://www.w3.org/TR/WCAG20-TECHS/F81.html F81: Failure of Success Criterion 1.4.1 due to identifying required or error fields using color differences only
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-required aria-required (property) | Supported States and Properties
	 */
	public function fixRequiredField(HTMLDOMElement $requiredField);
	
	/**
	 * Fix required fields.
	 * @see http://www.w3.org/TR/WCAG20-TECHS/H90.html H90: Indicating required form controls using label or legend
	 * @see http://www.w3.org/TR/2014/NOTE-WCAG20-TECHS-20140311/ARIA2 ARIA2: Identifying a required field with the aria-required property
	 * @see http://www.w3.org/TR/WCAG20-TECHS/F81.html F81: Failure of Success Criterion 1.4.1 due to identifying required or error fields using color differences only
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-required aria-required (property) | Supported States and Properties
	 */
	public function fixRequiredFields();
	
	/**
	 * Fix range field.
	 * @param \hatemile\util\HTMLDOMElement $rangeField The element that will be
	 * fixed.
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-valuemin aria-valuemin (property) | Supported States and Properties
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-valuemax aria-valuemax (property) | Supported States and Properties
	 * @see http://www.w3.org/WAI/GL/wiki/Using_WAI-ARIA_range_attributes_for_range_widgets_such_as_progressbar,_scrollbar,_slider,_and_spinbutton Using WAI-ARIA range attributes for range widgets such as progressbar, scrollbar, slider and spinbutton
	 * @see http://www.w3.org/WAI/GL/2013/WD-WCAG20-TECHS-20130711/ARIA3.html ARIA3: Identifying valid range information with the aria-valuemin and aria-valuemax properties
	 */
	public function fixRangeField(HTMLDOMElement $rangeField);
	
	/**
	 * Fix range fields.
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-valuemin aria-valuemin (property) | Supported States and Properties
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-valuemax aria-valuemax (property) | Supported States and Properties
	 * @see http://www.w3.org/WAI/GL/wiki/Using_WAI-ARIA_range_attributes_for_range_widgets_such_as_progressbar,_scrollbar,_slider,_and_spinbutton Using WAI-ARIA range attributes for range widgets such as progressbar, scrollbar, slider and spinbutton
	 * @see http://www.w3.org/WAI/GL/2013/WD-WCAG20-TECHS-20130711/ARIA3.html ARIA3: Identifying valid range information with the aria-valuemin and aria-valuemax properties
	 */
	public function fixRangeFields();
	
	/**
	 * Fix field associated with the label.
	 * @param \hatemile\util\HTMLDOMElement $label The element that will be
	 * fixed.
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-label aria-label (property) | Supported States and Properties
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-labelledby aria-labelledby (property) | Supported States and Properties
	 */
	public function fixLabel(HTMLDOMElement $label);
	
	/**
	 * Fix fields associated with the labels.
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-label aria-label (property) | Supported States and Properties
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-labelledby aria-labelledby (property) | Supported States and Properties
	 */
	public function fixLabels();
	
	/**
	 * Fix element to inform if has autocomplete and the type.
	 * @param \hatemile\util\HTMLDOMElement $element The element that will be
	 * fixed.
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-autocomplete aria-autocomplete (property) | Supported States and Properties
	 */
	public function fixAutoComplete(HTMLDOMElement $element);
	
	/**
	 * Fix elements to inform if has autocomplete and the type.
	 * @see http://www.w3.org/TR/wai-aria/states_and_properties#aria-autocomplete aria-autocomplete (property) | Supported States and Properties
	 */
	public function fixAutoCompletes();
}