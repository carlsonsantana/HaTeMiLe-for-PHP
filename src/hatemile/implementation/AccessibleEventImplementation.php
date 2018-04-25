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

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'AccessibleEvent.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'CommonFunctions.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'Configure.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'html',
    'HTMLDOMElement.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'util',
    'html',
    'HTMLDOMParser.php'
));

use \hatemile\AccessibleEvent;
use \hatemile\util\CommonFunctions;
use \hatemile\util\Configure;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;

/**
 * The AccessibleEventImplementation class is official implementation of
 * AccessibleEvent interface.
 */
class AccessibleEventImplementation implements AccessibleEvent
{

    /**
     * The id of script element that replace the event listener methods.
     * @var string
     */
    const ID_SCRIPT_EVENT_LISTENER = 'script-eventlistener';

    /**
     * The id of script element that contains the list of elements that has
     * inaccessible events.
     * @var string
     */
    const ID_LIST_IDS_SCRIPT = 'list-ids-script';

    /**
     * The id of script element that modify the events of elements.
     * @var string
     */
    const ID_FUNCTION_SCRIPT_FIX = 'id-function-script-fix';

    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $parser;

    /**
     * The prefix of generated ids.
     * @var string
     */
    protected $prefixId;

    /**
     * The state that indicates if the scripts used by solutions was added in
     * parser.
     * @var boolean
     */
    protected $mainScriptAdded;

    /**
     * The script element that contains the list of elements that has
     * inaccessible events.
     * @var \hatemile\util\html\HTMLDOMElement
     */
    protected $scriptList;

    /**
     * Initializes a new object that manipulate the accessibility of the
     * Javascript events of elements of parser.
     * @param \hatemile\util\html\HTMLDOMParser $parser The HTML parser.
     * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
     */
    public function __construct(HTMLDOMParser $parser, Configure $configure)
    {
        $this->parser = $parser;
        $this->prefixId = $configure->getParameter('prefix-generated-ids');
        $this->mainScriptAdded = false;
        $this->scriptList = null;
    }

    /**
     * Provide keyboard access for element, if it not has.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    protected function keyboardAccess(HTMLDOMElement $element)
    {
        if (!$element->hasAttribute('tabindex')) {
            $tag = $element->getTagName();
            if (($tag === 'A') && (!$element->hasAttribute('href'))) {
                $element->setAttribute('tabindex', '0');
            } elseif (
                ($tag !== 'A')
                && ($tag !== 'INPUT')
                && ($tag !== 'BUTTON')
                && ($tag !== 'SELECT')
                && ($tag !== 'TEXTAREA')
            ) {
                $element->setAttribute('tabindex', '0');
            }
        }
    }

    /**
     * Include the scripts used by solutions.
     */
    protected function generateMainScripts()
    {
        $head = $this->parser->find('head')->firstResult();
        if (
            ($head !== null)
            && ($this->parser->find(
                '#' . AccessibleEventImplementation::ID_SCRIPT_EVENT_LISTENER
            )->firstResult() === null)
        ) {
            $script = $this->parser->createElement('script');
            $script->setAttribute(
                'id',
                AccessibleEventImplementation::ID_SCRIPT_EVENT_LISTENER
            );
            $script->setAttribute('type', 'text/javascript');
            $script->appendText(file_get_contents(
                dirname(__FILE__) . '/../../js/eventlistener.js'
            ));
            if ($head->hasChildren()) {
                $head->getFirstElementChild()->insertBefore($script);
            } else {
                $head->appendElement($script);
            }
        }
        $local = $this->parser->find('body')->firstResult();
        if ($local !== null) {
            $this->scriptList = $this->parser->find(
                '#' . AccessibleEventImplementation::ID_LIST_IDS_SCRIPT
            )->firstResult();
            if ($this->scriptList === null) {
                $this->scriptList = $this->parser->createElement('script');
                $this->scriptList->setAttribute(
                    'id',
                    AccessibleEventImplementation::ID_LIST_IDS_SCRIPT
                );
                $this->scriptList->setAttribute('type', 'text/javascript');
                $this->scriptList->appendText('var activeElements = [];');
                $this->scriptList->appendText('var hoverElements = [];');
                $this->scriptList->appendText('var dragElements = [];');
                $this->scriptList->appendText('var dropElements = [];');
                $local->appendElement($this->scriptList);
            }
            if ($this->parser->find(
                '#' . AccessibleEventImplementation::ID_FUNCTION_SCRIPT_FIX
            )->firstResult() === null) {
                $scriptFunction = $this->parser->createElement('script');
                $scriptFunction->setAttribute(
                    'id',
                    AccessibleEventImplementation::ID_FUNCTION_SCRIPT_FIX
                );
                $scriptFunction->setAttribute('type', 'text/javascript');
                $scriptFunction->appendText(file_get_contents(
                    dirname(__FILE__) . '/../../js/include.js'
                ));
                $local->appendElement($scriptFunction);
            }
        }
        $this->mainScriptAdded = true;
    }

    /**
     * Add a type of event in element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     * @param string $event The type of event.
     */
    protected function addEventInElement($element, $event)
    {
        if (!$this->mainScriptAdded) {
            $this->generateMainScripts();
        }

        if ($this->scriptList !== null) {
            CommonFunctions::generateId($element, $this->prefixId);
            $this->scriptList->appendText(
                $event
                . "Elements.push('"
                . $element->getAttribute('id')
                . "');"
            );
        }
    }

    public function fixDrop(HTMLDOMElement $element)
    {
        $element->setAttribute('aria-dropeffect', 'none');

        $this->addEventInElement($element, 'drop');
    }

    public function fixDrag(HTMLDOMElement $element)
    {
        $this->keyboardAccess($element);

        $element->setAttribute('aria-grabbed', 'false');

        $this->addEventInElement($element, 'drag');
    }

    public function fixDragsandDrops()
    {
        $draggableElements = $this->parser->find(
            '[ondrag],[ondragstart],[ondragend]'
        )->listResults();
        foreach ($draggableElements as $draggableElement) {
            if (CommonFunctions::isValidElement($draggableElement)) {
                $this->fixDrag($draggableElement);
            }
        }
        $droppableElements = $this->parser->find(
            '[ondrop],[ondragenter],[ondragleave],[ondragover]'
        )->listResults();
        foreach ($droppableElements as $droppableElement) {
            if (CommonFunctions::isValidElement($droppableElement)) {
                $this->fixDrop($droppableElement);
            }
        }
    }

    public function fixHover(HTMLDOMElement $element)
    {
        $this->keyboardAccess($element);

        $this->addEventInElement($element, 'hover');
    }

    public function fixHovers()
    {
        $elements = $this->parser->find(
            '[onmouseover],[onmouseout]'
        )->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->fixHover($element);
            }
        }
    }

    public function fixActive(HTMLDOMElement $element)
    {
        $this->keyboardAccess($element);

        $this->addEventInElement($element, 'active');
    }

    public function fixActives()
    {
        $elements = $this->parser->find(
            '[onclick],[onmousedown],[onmouseup],[ondblclick]'
        )->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->fixActive($element);
            }
        }
    }
}
