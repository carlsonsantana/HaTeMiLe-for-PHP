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

require_once dirname(__FILE__) . '/util/html/HTMLDOMElement.php';

use \hatemile\util\html\HTMLDOMElement;

/**
 * The AccessibleEvent interface fixes accessibility problems associated
 * with JavaScript events in elements.
 */
interface AccessibleEvent {
	
	/**
	 * Provide a solution for the element that has drop events.
	 * @param \hatemile\util\html\HTMLDOMElement $element The element with drop
	 * event.
	 */
	public function fixDrop(HTMLDOMElement $element);
	
	/**
	 * Provide a solution for the element that has drag events.
	 * @param \hatemile\util\html\HTMLDOMElement $element The element with drag
	 * event.
	 */
	public function fixDrag(HTMLDOMElement $element);
	
	/**
	 * Provide a solution for elements that has Drag-and-Drop events.
	 */
	public function fixDragsandDrops();
	
	/**
	 * Provide a solution for the element that has inaccessible hover events.
	 * @param \hatemile\util\html\HTMLDOMElement $element The element with hover
	 * event.
	 */
	public function fixHover(HTMLDOMElement $element);
	
	/**
	 * Provide a solution for elements that has inaccessible hover events.
	 */
	public function fixHovers();
	
	/**
	 * Provide a solution for the element that has inaccessible active events.
	 * @param \hatemile\util\html\HTMLDOMElement $element The element with active
	 * event.
	 */
	public function fixActive(HTMLDOMElement $element);
	
	/**
	 * Provide a solution for elements that has inaccessible active events.
	 */
	public function fixActives();
}