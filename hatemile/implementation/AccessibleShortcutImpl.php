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

require_once dirname(__FILE__) . '/../util/HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../util/Configure.php';
require_once dirname(__FILE__) . '/../util/HTMLDOMElement.php';
require_once dirname(__FILE__) . '/../AccessibleShortcut.php';

use hatemile\util\HTMLDOMParser;
use hatemile\util\Configure;
use hatemile\util\HTMLDOMElement;
use hatemile\AccessibleShortcut;

/**
 * The AccessibleShortcutImpl class is official implementation of
 * AccessibleShortcut interface.
 * @version 2014-07-23
 */
class AccessibleShortcutImpl implements AccessibleShortcut {
	
	/**
	 * The HTML parser.
	 * @var \hatemile\util\HTMLDOMParser
	 */
	protected $parser;
	
	/**
	 * The id of list element that contains the description of shortcuts.
	 * @var string
	 */
	protected $idContainerShortcuts;
	
	/**
	 * The id of link element that skip the list of shortcuts.
	 * @var string
	 */
	protected $idSkipLinkContainerShortcuts;
	
	/**
	 * The id of anchor element after the list of shortcuts.
	 * @var string
	 */
	protected $idSkipContainerShortcuts;
	
	/**
	 * The id of text that precede the shortcuts descriptions.
	 * @var string
	 */
	protected $idTextShortcuts;
	
	/**
	 * The text of link element that skip the list of shortcuts.
	 * @var string
	 */
	protected $textSkipLinkContainerShortcuts;
	
	/**
	 * The text that precede the shortcuts descriptions.
	 * @var string
	 */
	protected $textShortcuts;
	
	/**
	 * The name of attribute that link the list item element with the shortcut.
	 * @var string
	 */
	protected $dataAccessKey;
	
	/**
	 * The name of attribute for that the element not can be modified by
	 * HaTeMiLe.
	 * @var string
	 */
	protected $dataIgnore;
	
	/**
	 * The browser shortcut prefix.
	 * @var string
	 */
	protected $prefix;
	
	/**
	 * Standart browser prefix.
	 * @var string
	 */
	protected $standartPrefix;
	
	/**
	 * The list element of shortcuts of page.
	 * @var \hatemile\util\HTMLDOMElement
	 */
	protected $list;
	
	/**
	 * The state that indicates if the list of shortcuts of page was added.
	 * @var boolean
	 */
	protected $listAdded;
	
	/**
	 * Initializes a new object that manipulate the accessibility of the
	 * shortcuts of parser.
	 * @param \hatemile\util\HTMLDOMParser $parser The HTML parser.
	 * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
	 * @param string $userAgent The user agent of the user.
	 */
	public function __construct(HTMLDOMParser $parser, Configure $configure, $userAgent) {
		$this->parser = $parser;
		$this->idContainerShortcuts = $configure->getParameter('id-container-shortcuts');
		$this->idSkipLinkContainerShortcuts
				= $configure->getParameter('id-skip-link-container-shortcuts');
		$this->idSkipContainerShortcuts = $configure->getParameter('id-skip-container-shortcuts');
		$this->idTextShortcuts = $configure->getParameter('id-text-shortcuts');
		$this->textSkipLinkContainerShortcuts
				= $configure->getParameter('text-skip-container-shortcuts');
		$this->textShortcuts = $configure->getParameter('text-shortcuts');
		$this->standartPrefix = $configure->getParameter('text-standart-shortcut-prefix');
		$this->dataAccessKey = 'data-' . $configure->getParameter('data-accesskey');
		$this->dataIgnore = 'data-' . $configure->getParameter('data-ignore');
		$this->listAdded = false;
		
		if ($userAgent !== null) {
			$userAgent = strtolower($userAgent);
			$opera = strpos($userAgent, 'opera') !== false;
			$mac = strpos($userAgent, 'mac') !== false;
			$konqueror = strpos($userAgent, 'konqueror') !== false;
			$spoofer = strpos($userAgent, 'spoofer') !== false;
			$safari = strpos($userAgent, 'applewebkit') !== false;
			$windows = strpos($userAgent, 'windows') !== false;
			$chrome = strpos($userAgent, 'chrome') !== false;
			$firefox = preg_match($userAgent, "/firefox\/[2-9]|minefield\/3/");
			$ie = (strpos($userAgent, 'msie') !== false) || (strpos($userAgent, 'trident') !== false);
			
			if ($opera) {
				$this->prefix = 'SHIFT + ESC';
			} else if ($chrome && $mac && !$spoofer) {
				$this->prefix = 'CTRL + OPTION';
			} else if ($safari && !$windows && !$spoofer) {
				$this->prefix = 'CTRL + ALT';
			} else if (!$windows && ($safari || $mac || $konqueror)) {
				$this->prefix = 'CTRL';
			} else if ($firefox) {
				$this->prefix = 'ALT + SHIFT';
			} else if ($chrome || $ie) {
				$this->prefix = 'ALT';
			} else {
				$this->prefix = $this->standartPrefix;
			}
		} else {
			$this->prefix = $this->standartPrefix;
		}
	}
	
	/**
	 * Returns the description of element.
	 * @param \hatemile\util\HTMLDOMElement $element The element with
	 * description.
	 * @return string The description of element.
	 */
	protected function getDescription(HTMLDOMElement $element) {
		if ($element->hasAttribute('title')) {
			$description = $element->getAttribute('title');
		} else if ($element->hasAttribute('aria-label')) {
			$description = $element->getAttribute('aria-label');
		} else if ($element->hasAttribute('alt')) {
			$description = $element->getAttribute('alt');
		} else if ($element->hasAttribute('label')) {
			$description = $element->getAttribute('label');
		} else if (($element->hasAttribute('aria-labelledby'))
				|| ($element->hasAttribute('aria-describedby'))) {
			if ($element->hasAttribute('aria-labelledby')) {
				$descriptionIds = preg_split("/[ \n\t\r]+/", $element->getAttribute('aria-labelledby'));
			} else {
				$descriptionIds = preg_split("/[ \n\t\r]+/", $element->getAttribute('aria-describedby'));
			}
			foreach ($descriptionIds as $descriptionId) {
				$elementDescription = $this->parser->find('#' . $descriptionId)->firstResult();
				if ($elementDescription !== null) {
					$description = $elementDescription->getTextContent();
					break;
				}
			}
		} else if ($element->getTagName() === 'INPUT') {
			$type = strtolower($element->getAttribute('type'));
			if ((($type === 'button') || ($type === 'submit') || ($type === 'reset'))
					&& ($element->hasAttribute('value'))) {
				$description = $element->getAttribute('value');
			}
		}
		if (empty($description)) {
			$description = $element->getTextContent();
		}
		return \trim(\preg_replace("/[ \n\r\t]+/", ' ', $description));
	}
	
	/**
	 * Generate the list of shortcuts of page.
	 * @return \hatemile\util\HTMLDOMElement The list of shortcuts of page.
	 */
	protected function generateList() {
		$local = $this->parser->find('body')->firstResult();
		$htmlList = null;
		if ($local !== null) {
			$container = $this->parser->find('#' . $this->idContainerShortcuts)->firstResult();
			if ($container === null) {
				$container = $this->parser->createElement('div');
				$container->setAttribute('id', $this->idContainerShortcuts);
				$firstChild = $local->getFirstElementChild();
				$firstChild->insertBefore($container);
				
				$anchorJump = $this->parser->createElement('a');
				$anchorJump->setAttribute('id', $this->idSkipLinkContainerShortcuts);
				$anchorJump->setAttribute('href', '#' . $this->idSkipContainerShortcuts);
				$anchorJump->appendText($this->textSkipLinkContainerShortcuts);
				$container->insertBefore($anchorJump);
				
				$anchor = $this->parser->createElement('a');
				$anchor->setAttribute('name', $this->idSkipContainerShortcuts);
				$anchor->setAttribute('id', $this->idSkipContainerShortcuts);
				$firstChild->insertBefore($anchor);
				
				$textContainer = $this->parser->createElement('span');
				$textContainer->setAttribute('id', $this->idTextShortcuts);
				$textContainer->appendText($this->textShortcuts);
				$container->appendElement($textContainer);
			}
			$htmlList = $this->parser->find($container)->findChildren('ul')->firstResult();
			if ($htmlList === null) {
				$htmlList = $this->parser->createElement('ul');
				$container->appendElement($htmlList);
			}
		}
		$this->listAdded = true;
		
		return $htmlList;
	}
	
	public function getPrefix() {
		return $this->prefix;
	}
	
	public function fixShortcut(HTMLDOMElement $element) {
		if ($element->hasAttribute('accesskey')) {
			$description = $this->getDescription($element);
			if (!$element->hasAttribute('title')) {
				$element->setAttribute('title', $description);
			}
			
			if (!$this->listAdded) {
				$this->list = $this->generateList();
			}
			
			if ($this->list !== null) {
				$keys = preg_split("/[ \n\t\r]+/", $element->getAttribute('accesskey'));
				foreach ($keys as $key) {
					$key = strtoupper($key);
					$firstChildren = $this->parser->find($this->list)
							->findChildren('[' . $this->dataAccessKey . '=' . $key . ']')->firstResult();
					if ($firstChildren === null) {
						$item = $this->parser->createElement('li');
						$item->setAttribute($this->dataAccessKey, $key);
						$item->appendText($this->prefix . ' + ' . $key . ': ' . $description);
						$this->list->appendElement($item);
					}
				}
			}
		}
	}
	
	public function fixShortcuts() {
		$elements = $this->parser->find('[accesskey]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixShortcut($element);
			}
		}
	}
}