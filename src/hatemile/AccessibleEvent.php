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

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'util',
    'html',
    'HTMLDOMElement.php'
));

use \hatemile\util\html\HTMLDOMElement;

/**
 * The AccessibleEvent interface improve the accessibility, making elements
 * events available from a keyboard.
 */
interface AccessibleEvent
{

    /**
     * Make the drop events of element available from a keyboard.
     * @param \hatemile\util\html\HTMLDOMElement $element The element with drop
     * event.
     */
    public function makeAccessibleDropEvents(HTMLDOMElement $element);

    /**
     * Make the drag events of element available from a keyboard.
     * @param \hatemile\util\html\HTMLDOMElement $element The element with drag
     * event.
     */
    public function makeAccessibleDragEvents(HTMLDOMElement $element);

    /**
     * Make all Drag-and-Drop events of page available from a keyboard.
     */
    public function makeAccessibleAllDragandDropEvents();

    /**
     * Make the hover events of element available from a keyboard.
     * @param \hatemile\util\html\HTMLDOMElement $element The element with hover
     * event.
     */
    public function makeAccessibleHoverEvents(HTMLDOMElement $element);

    /**
     * Make all hover events of page available from a keyboard.
     */
    public function makeAccessibleAllHoverEvents();

    /**
     * Make the click events of element available from a keyboard.
     * @param \hatemile\util\html\HTMLDOMElement $element The element with click
     * events.
     */
    public function makeAccessibleClickEvents(HTMLDOMElement $element);

    /**
     * Make all click events of page available from a keyboard.
     */
    public function makeAccessibleAllClickEvents();
}
