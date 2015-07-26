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

namespace hatemile;

require_once dirname(__FILE__) . '/util/HTMLDOMElement.php';
require_once dirname(__FILE__) . '/util/Skipper.php';

use \hatemile\util\HTMLDOMElement;
use \hatemile\util\Skipper;

/**
 * The AccessibleNavigation interface fixes accessibility problems associated
 * with navigation.
 */
interface AccessibleNavigation {
	
	/**
	 * Display the shortcuts of element.
	 * @param \hatemile\util\HTMLDOMElement $element The element with shortcuts.
	 */
	public function fixShortcut(HTMLDOMElement $element);
	
	/**
	 * Display the shortcuts of elements.
	 */
	public function fixShortcuts();
	
	/**
	 * Provide content skipper for element.
	 * @param \hatemile\util\HTMLDOMElement $element The element.
	 * @param \hatemile\util\Skipper $skipper The skipper.
	 */
	public function fixSkipper(HTMLDOMElement $element, Skipper $skipper);
	
	/**
	 * Provide content skippers.
	 */
	public function fixSkippers();
	
	/**
	 * Provide a navigation by heading.
	 * @param \hatemile\util\HTMLDOMElement $element The heading element.
	 */
	public function fixHeading(HTMLDOMElement $element);
	
	/**
	 * Provide a navigation by headings.
	 */
	public function fixHeadings();
}