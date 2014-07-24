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
 * The AccessibleEvent interface fix the problems of accessibility associated
 * with Javascript events in the elements.
 * @version 2014-07-23
 */
interface AccessibleEvent {
	
	/**
	 * Fix some problem of accessibility in the events that are called when an
	 * element is hovered.
	 * @param \hatemile\util\HTMLDOMElement $element The element that will be
	 * fixed.
	 * @see http://www.w3.org/TR/WCAG20-TECHS/G90.html G90: Providing keyboard-triggered event handlers
	 * @see http://www.w3.org/TR/WCAG20-TECHS/G202.html G202: Ensuring keyboard control for all functionality
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR2.html SCR2: Using redundant keyboard and mouse event handlers
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR20.html SCR20: Using both keyboard and other device-specific functions
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR29.html SCR29: Adding keyboard-accessible actions to static HTML elements
	 */
	public function fixOnHover(HTMLDOMElement $element);
	
	/**
	 * Fix some problem of accessibility in the events that are called when any
	 * element of page is hovered.
	 * @see http://www.w3.org/TR/WCAG20-TECHS/G90.html G90: Providing keyboard-triggered event handlers
	 * @see http://www.w3.org/TR/WCAG20-TECHS/G202.html G202: Ensuring keyboard control for all functionality
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR2.html SCR2: Using redundant keyboard and mouse event handlers
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR20.html SCR20: Using both keyboard and other device-specific functions
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR29.html SCR29: Adding keyboard-accessible actions to static HTML elements
	 */
	public function fixOnHovers();
	
	/**
	 * Fix some problem of accessibility in the events that are called when an
	 * element is actived.
	 * @param \hatemile\util\HTMLDOMElement $element The element that will be
	 * fixed.
	 * @see http://www.w3.org/TR/WCAG20-TECHS/G90.html G90: Providing keyboard-triggered event handlers
	 * @see http://www.w3.org/TR/WCAG20-TECHS/G202.html G202: Ensuring keyboard control for all functionality
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR2.html SCR2: Using redundant keyboard and mouse event handlers
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR20.html SCR20: Using both keyboard and other device-specific functions
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR29.html SCR29: Adding keyboard-accessible actions to static HTML elements
	 */
	public function fixOnActive(HTMLDOMElement $element);
	
	/**
	 * Fix some problem of accessibility in the events that are called when any
	 * element of page is actived.
	 * @see http://www.w3.org/TR/WCAG20-TECHS/G90.html G90: Providing keyboard-triggered event handlers
	 * @see http://www.w3.org/TR/WCAG20-TECHS/G202.html G202: Ensuring keyboard control for all functionality
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR2.html SCR2: Using redundant keyboard and mouse event handlers
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR20.html SCR20: Using both keyboard and other device-specific functions
	 * @see http://www.w3.org/TR/WCAG20-TECHS/SCR29.html SCR29: Adding keyboard-accessible actions to static HTML elements
	 */
	public function fixOnActives();
}