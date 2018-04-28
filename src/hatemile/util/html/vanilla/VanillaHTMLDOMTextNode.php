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

namespace hatemile\util\html\vanilla;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'HTMLDOMTextNode.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'VanillaHTMLDOMTextNode.php'
));

use \hatemile\util\html\HTMLDOMTextNode;
use \hatemile\util\html\vanilla\VanillaHTMLDOMNode;

/**
 * The VanillaHTMLDOMTextNode class is official implementation of
 * \hatemile\util\html\HTMLDOMTextNode for the DOMText.
 */
class VanillaHTMLDOMTextNode extends VanillaHTMLDOMNode implements
    HTMLDOMTextNode
{

    /**
     * The vanilla TextNode encapsulated.
     * @var \DOMText
     */
    protected $textNode;

    /**
     * Initializes a new object that encapsulate the DOMText.
     * @param \DOMText The DOMText.
     */
    public function __construct(\DOMText $textNode)
    {
        parent::__construct($textNode);
        $this->textNode = $textNode;
    }

    public function getTextContent()
    {
        return $this->textNode->wholeText;
    }

    public function setTextContent($text)
    {
        $newTextNode = $this->textNode->ownerDocument->createTextNode($text);
        $parent = $this->getParentElement()->getData();
        $parent->replaceChild($newTextNode, $this->textNode);
        $this->setData($newTextNode);
    }

    public function appendText($text)
    {
        $this->setTextContent($this->getTextContent() . $text);
        return $this;
    }
}
