<?php
/**
 * Class AccessibleEventImplementation.
 * 
 * @package hatemile\implementation
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
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
    'IDGenerator.php'
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
use \hatemile\util\IDGenerator;
use \hatemile\util\html\HTMLDOMElement;
use \hatemile\util\html\HTMLDOMParser;

/**
 * The AccessibleEventImplementation class is official implementation of
 * AccessibleEvent.
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
     * The ID of script element that contains the common functions of scripts.
     * @var string
     */
    const ID_SCRIPT_COMMON_FUNCTIONS = 'hatemile-common-functions';

    /**
     * The HTML parser.
     * @var \hatemile\util\html\HTMLDOMParser
     */
    protected $parser;

    /**
     * The id generator.
     * @var \hatemile\util\IDGenerator
     */
    protected $idGenerator;

    /**
     * The state that indicates if the scripts used by solutions was added in
     * parser.
     * @var bool
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
     */
    public function __construct(HTMLDOMParser $parser)
    {
        $this->parser = $parser;
        $this->idGenerator = new IDGenerator('event');
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
        if ($head !== null) {
            $commonFunctionsScript = $this->parser->find(
                '#' .
                AccessibleEventImplementation::ID_SCRIPT_COMMON_FUNCTIONS
            )->firstResult();
            if ($commonFunctionsScript === null) {
                $commonFunctionsScript = $this->parser->createElement('script');
                $commonFunctionsScript->setAttribute(
                    'id',
                    AccessibleEventImplementation::ID_SCRIPT_COMMON_FUNCTIONS
                );
                $commonFunctionsScript->setAttribute('type', 'text/javascript');
                $commonFunctionsScript->appendText(file_get_contents(
                    join(DIRECTORY_SEPARATOR, array(
                        dirname(dirname(dirname(__FILE__))),
                        'js',
                        'common.js'
                    ))
                ));
                $head->prependElement($commonFunctionsScript);
            }
            if ($this->parser->find(
                '#' .
                AccessibleEventImplementation::ID_SCRIPT_EVENT_LISTENER
            )->firstResult() === null) {
                $script = $this->parser->createElement('script');
                $script->setAttribute(
                    'id',
                    AccessibleEventImplementation::ID_SCRIPT_EVENT_LISTENER
                );
                $script->setAttribute('type', 'text/javascript');
                $script->appendText(file_get_contents(
                    join(DIRECTORY_SEPARATOR, array(
                        dirname(dirname(dirname(__FILE__))),
                        'js',
                        'eventlistener.js'
                    ))
                ));
                $commonFunctionsScript->insertAfter($script);
            }
        }
        $local = $this->parser->find('body')->firstResult();
        if ($local !== null) {
            $this->scriptList = $this->parser->find(
                '#' .
                AccessibleEventImplementation::ID_LIST_IDS_SCRIPT
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
                '#' .
                AccessibleEventImplementation::ID_FUNCTION_SCRIPT_FIX
            )->firstResult() === null) {
                $scriptFunction = $this->parser->createElement('script');
                $scriptFunction->setAttribute(
                    'id',
                    AccessibleEventImplementation::ID_FUNCTION_SCRIPT_FIX
                );
                $scriptFunction->setAttribute('type', 'text/javascript');
                $scriptFunction->appendText(file_get_contents(
                    join(DIRECTORY_SEPARATOR, array(
                        dirname(dirname(dirname(__FILE__))),
                        'js',
                        'include.js'
                    ))
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
            $this->idGenerator->generateId($element);
            $this->scriptList->appendText(
                $event .
                'Elements.push("' .
                $element->getAttribute('id') .
                '");'
            );
        }
    }

    public function makeAccessibleDropEvents(HTMLDOMElement $element)
    {
        $element->setAttribute('aria-dropeffect', 'none');

        $this->addEventInElement($element, 'drop');
    }

    public function makeAccessibleDragEvents(HTMLDOMElement $element)
    {
        $this->keyboardAccess($element);

        $element->setAttribute('aria-grabbed', 'false');

        $this->addEventInElement($element, 'drag');
    }

    public function makeAccessibleAllDragandDropEvents()
    {
        $draggableElements = $this->parser->find(
            '[ondrag],[ondragstart],[ondragend]'
        )->listResults();
        foreach ($draggableElements as $draggableElement) {
            if (CommonFunctions::isValidElement($draggableElement)) {
                $this->makeAccessibleDragEvents($draggableElement);
            }
        }
        $droppableElements = $this->parser->find(
            '[ondrop],[ondragenter],[ondragleave],[ondragover]'
        )->listResults();
        foreach ($droppableElements as $droppableElement) {
            if (CommonFunctions::isValidElement($droppableElement)) {
                $this->makeAccessibleDropEvents($droppableElement);
            }
        }
    }

    public function makeAccessibleHoverEvents(HTMLDOMElement $element)
    {
        $this->keyboardAccess($element);

        $this->addEventInElement($element, 'hover');
    }

    public function makeAccessibleAllHoverEvents()
    {
        $elements = $this->parser->find(
            '[onmouseover],[onmouseout]'
        )->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->makeAccessibleHoverEvents($element);
            }
        }
    }

    public function makeAccessibleClickEvents(HTMLDOMElement $element)
    {
        $this->keyboardAccess($element);

        $this->addEventInElement($element, 'active');
    }

    public function makeAccessibleAllClickEvents()
    {
        $elements = $this->parser->find(
            '[onclick],[onmousedown],[onmouseup],[ondblclick]'
        )->listResults();
        foreach ($elements as $element) {
            if (CommonFunctions::isValidElement($element)) {
                $this->makeAccessibleClickEvents($element);
            }
        }
    }
}
