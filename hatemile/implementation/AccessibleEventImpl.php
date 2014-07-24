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

require_once dirname(__FILE__) . '/../util/HTMLDOMElement.php';
require_once dirname(__FILE__) . '/../util/HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../util/Configure.php';
require_once dirname(__FILE__) . '/../AccessibleEvent.php';
require_once dirname(__FILE__) . '/../util/CommonFunctions.php';

use hatemile\util\HTMLDOMElement;
use hatemile\util\HTMLDOMParser;
use hatemile\util\Configure;
use hatemile\AccessibleEvent;
use hatemile\util\CommonFunctions;

/**
 * The AccessibleEventImpl class is official implementation of AccessibleEvent
 * interface.
 * @version 2014-07-23
 */
class AccessibleEventImpl implements AccessibleEvent {
	
	/**
	 * The HTML parser.
	 * @var \hatemile\util\HTMLDOMParser
	 */
	protected $parser;
	
	/**
	 * The id of script element that has the functions that put events in the
	 * elements.
	 * @var string
	 */
	protected $idScriptEvent;
	
	/**
	 * The id of script element that has the list of elements that will have its
	 * events modified.
	 * @var string
	 */
	protected $idListIdsScriptOnActive;
	
	/**
	 * The id of script element that modify the events of elements.
	 * @var string
	 */
	protected $idFunctionScriptFixOnActive;
	
	/**
	 * The prefix of generated id.
	 * @var string
	 */
	protected $prefixId;
	
	/**
	 * The name of attribute for that the element not can be modified by
	 * HaTeMiLe.
	 * @var string
	 */
	protected $dataIgnore;
	
	/**
	 * The state that indicates if the main script was added in parser.
	 * @var boolean
	 */
	protected $mainScriptAdded;
	
	/**
	 * The state that indicates if the other scripts was added in parser.
	 * @var boolean
	 */
	protected $otherScriptsAdded;
	
	/**
	 * The script element that contains the list of elements that will have its
	 * events modified.
	 * @var \hatemile\util\HTMLDOMElement
	 */
	protected $scriptList;
	
	/**
	 * Initializes a new object that manipulate the accessibility of the
	 * Javascript events of elements of parser.
	 * @param \hatemile\util\HTMLDOMParser $parser The HTML parser.
	 * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
	 */
	public function __construct(HTMLDOMParser $parser, Configure $configure) {
		$this->parser = $parser;
		$this->prefixId = $configure->getParameter('prefix-generated-ids');
		$this->idScriptEvent = $configure->getParameter('id-script-event');
		$this->idListIdsScriptOnActive = $configure->getParameter('id-list-ids-script-onactive');
		$this->idFunctionScriptFixOnActive = $configure->getParameter('id-function-script-fix-onactive');
		$this->dataIgnore = 'data-' . $configure->getParameter('data-ignore');
		$this->mainScriptAdded = false;
		$this->otherScriptsAdded = false;
	}
	
	/**
	 * Generate the main script in parser.
	 */
	protected function generateMainScript() {
		$local = $this->parser->find('head')->firstResult();
		if ($local === null) {
			$local = $this->parser->find('body')->firstResult();
		}
		if (($local !== null)
				&& ($this->parser->find('#' . $this->idScriptEvent)->firstResult() === null)) {
			$script = $this->parser->createElement('script');
			$script->setAttribute('id', $this->idScriptEvent);
			$script->setAttribute('type', 'text/javascript');
			$javascript = 'function onFocusEvent(e){if(e.onmouseover!=undefined){try{e.onmouseover();}catch(x){}}}function onBlurEvent(e){if(e.onmouseout!=undefined){try{e.onmouseout();}catch(x){}}}function isEnter(k){var n="\\n".charCodeAt(0);var r="\\r".charCodeAt(0);return ((k==n)||(k==r));}function onKeyDownEvent(l,v){if(isEnter(v.keyCode)&&(l.onmousedown!=undefined)){try{l.onmousedown();}catch(x){}}}function onKeyPressEvent(l,v){if(isEnter(v.keyCode)){if(l.onclick!=undefined){try{l.click();}catch(x){}}else if(l.ondblclick!=undefined){try{l.ondblclick();}catch(x){}}}}function onKeyUpEvent(l,v){if(isEnter(v.keyCode)&&(l.onmouseup!=undefined)){try{l.onmouseup();}catch(x){}}}';
			$script->appendText($javascript);
			$local->appendElement($script);
		}
		$this->mainScriptAdded = true;
	}
	
	/**
	 * Generate the other scripts in parser.
	 */
	protected function generateOtherScripts() {
		$local = $this->parser->find('body')->firstResult();
		if ($local !== null) {
			$this->scriptList = $this->parser->find('#' . $this->idListIdsScriptOnActive)->firstResult();
			if ($this->scriptList === null) {
				$this->scriptList = $this->parser->createElement('script');
				$this->scriptList->setAttribute('id', $this->idListIdsScriptOnActive);
				$this->scriptList->setAttribute('type', 'text/javascript');
				$this->scriptList->appendText('var s=[];');
				$local->appendElement($this->scriptList);
			}
			if ($this->parser->find('#' . $this->idFunctionScriptFixOnActive)->firstResult() === null) {
				$scriptFunction = $this->parser->createElement('script');
				$scriptFunction->setAttribute('id', $this->idFunctionScriptFixOnActive);
				$scriptFunction->setAttribute('type', 'text/javascript');
				$javascript = 'var e;for(var i=0,l=s.length;i<l;i++){e=document.getElementById(s[i]);if(e.onkeypress==undefined){e.onkeypress=function(v){onKeyPressEvent(e,v);};}if(e.onkeyup==undefined){e.onkeyup=function(v){onKeyUpEvent(e,v);};}if(e.onkeydown==undefined){e.onkeydown=function(v){onKeyDownEvent(e,v);};}}';
				$scriptFunction->appendText($javascript);
				$local->appendElement($scriptFunction);
			}
		}
		$this->otherScriptsAdded = true;
	}
	
	/**
	 * Add the id of element in list of elements that will have its events
	 * modified.
	 * @param \hatemile\util\HTMLDOMElement $element The element with id.
	 */
	protected function addEventInElement($element) {
		if (!$this->otherScriptsAdded) {
			$this->generateOtherScripts();
		}
		
		if ($this->scriptList !== null) {
			CommonFunctions::generateId($element, $this->prefixId);
			$this->scriptList->appendText("s.push('" . $element->getAttribute('id') . "');");
		} else {
			if (!$element->hasAttribute('onkeypress')) {
				$element->setAttribute('onkeypress', 'try{onKeyPressEvent(this,event);}catch(x){}');
			}
			if (!$element->hasAttribute('onkeyup')) {
				$element->setAttribute('onkeyup', 'try{onKeyUpEvent(this,event);}catch(x){}');
			}
			if (!$element->hasAttribute('onkeydown')) {
				$element->setAttribute('onkeydown', 'try{onKeyDownEvent(this,event);}catch(x){}');
			}
		}
	}
	
	public function fixOnHover(HTMLDOMElement $element) {
		$tag = $element->getTagName();
		if (!(($tag === 'INPUT') || ($tag === 'BUTTON') || ($tag === 'A') || ($tag === 'SELECT')
				|| ($tag === 'TEXTAREA') || ($element->hasAttribute('tabindex')))) {
			$element->setAttribute('tabindex', '0');
		}
		
		if (!$this->mainScriptAdded) {
			$this->generateMainScript();
		}
		
		if (!$element->hasAttribute('onfocus')) {
			$element->setAttribute('onfocus', 'onFocusEvent(this);');
		}
		if (!$element->hasAttribute('onblur')) {
			$element->setAttribute('onblur', 'onBlurEvent(this);');
		}
	}
	
	public function fixOnHovers() {
		$elements = $this->parser->find('[onmouseover],[onmouseout]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixOnHover($element);
			}
		}
	}
	
	public function fixOnActive(HTMLDOMElement $element) {
		$tag = $element->getTagName();
		if (!(($tag === 'INPUT') || ($tag === 'BUTTON') || ($tag === 'A'))) {
			if (!(($element->hasAttribute('tabindex')) || ($tag === 'SELECT')
					|| ($tag === 'TEXTAREA'))) {
				$element->setAttribute('tabindex', '0');
			}
			
			if (!$this->mainScriptAdded) {
				$this->generateMainScript();
			}
			
			$this->addEventInElement($element);
		}
	}
	
	public function fixOnActives() {
		$elements = $this->parser->find('[onclick],[onmousedown],[onmouseup],[ondblclick]')
				->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixOnActive($element);
			}
		}
	}
}