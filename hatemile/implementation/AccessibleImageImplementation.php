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

require_once dirname(__FILE__) . '/../AccessibleImage.php';
require_once dirname(__FILE__) . '/../util/HTMLDOMElement.php';
require_once dirname(__FILE__) . '/../util/HTMLDOMParser.php';
require_once dirname(__FILE__) . '/../util/Configure.php';
require_once dirname(__FILE__) . '/../util/CommonFunctions.php';

use \hatemile\AccessibleImage;
use \hatemile\util\HTMLDOMElement;
use \hatemile\util\HTMLDOMParser;
use \hatemile\util\Configure;
use \hatemile\util\CommonFunctions;

/**
 * The AccessibleImageImplementation class is official implementation of
 * AccessibleImage interface.
 */
class AccessibleImageImplementation implements AccessibleImage {
	
	/**
	 * The HTML parser.
	 * @var \hatemile\util\HTMLDOMParser
	 */
	protected $parser;
	
	/**
	 * The prefix of generated ids.
	 * @var string
	 */
	protected $prefixId;
	
	/**
	 * The HTML class of element for show the long description of image.
	 * @var string
	 */
	protected $classLongDescriptionLink;
	
	/**
	 * The prefix of content of long description.
	 * @var string
	 */
	protected $prefixLongDescriptionLink;
	
	/**
	 * The suffix of content of long description.
	 * @var string
	 */
	protected $suffixLongDescriptionLink;
	
	/**
	 * The name of attribute that link the anchor of long description with the
	 * image.
	 * @var string
	 */
	protected $dataLongDescriptionForImage;
	
	/**
	 * The name of attribute for not modify the elements.
	 * @var string
	 */
	protected $dataIgnore;
	
	/**
	 * Initializes a new object that manipulate the accessibility of the images
	 * of parser.
	 * @param \hatemile\util\HTMLDOMParser $parser The HTML parser.
	 * @param \hatemile\util\Configure $configure The configuration of HaTeMiLe.
	 */
	public function __construct(HTMLDOMParser $parser, Configure $configure) {
		$this->parser = $parser;
		$this->prefixId = $configure->getParameter('prefix-generated-ids');
		$this->classLongDescriptionLink = 'longdescription-link';
		$this->dataLongDescriptionForImage = 'data-longdescriptionfor';
		$this->dataIgnore = 'data-ignoreaccessibilityfix';
		$this->prefixLongDescriptionLink = $configure->getParameter('prefix-longdescription');
		$this->suffixLongDescriptionLink = $configure->getParameter('suffix-longdescription');
	}
	
	public function fixLongDescription(HTMLDOMElement $element) {
		if ($element->hasAttribute('longdesc')) {
			CommonFunctions::generateId($element, $this->prefixId);
			$id = $element->getAttribute('id');
			if ($this->parser->find('[' . $this->dataLongDescriptionForImage . '="' . $id . '"]')
					->firstResult() !== null) {
				if ($element->hasAttribute('alt')) {
					$text = $this->prefixLongDescriptionLink . ' ' . $element->getAttribute('alt')
							. ' ' . $this->suffixLongDescriptionLink;
				} else {
					$text = $this->prefixLongDescriptionLink . ' ' . $this->suffixLongDescriptionLink;
				}
				$anchor = $this->parser->createElement('a');
				$anchor->setAttribute('href', $element->getAttribute('longdesc'));
				$anchor->setAttribute('target', '_blank');
				$anchor->setAttribute($this->dataLongDescriptionForImage, $id);
				$anchor->setAttribute('class', $this->classLongDescriptionLink);
				$anchor->appendText(\trim($text));
				$element->insertAfter($anchor);
			}
		}
	}
	
	public function fixLongDescriptions() {
		$elements = $this->parser->find('[longdesc]')->listResults();
		foreach ($elements as $element) {
			if (!$element->hasAttribute($this->dataIgnore)) {
				$this->fixLongDescription($element);
			}
		}
	}
}