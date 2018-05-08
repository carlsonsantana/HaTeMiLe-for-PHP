<?php
/**
 * Interface AccessibleEvent.
 * 
 * @package hatemile
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
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
