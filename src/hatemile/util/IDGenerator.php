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

namespace hatemile\util;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'html',
    'HTMLDOMElement.php'
));

use \hatemile\util\html\HTMLDOMElement;

class IDGenerator
{

    /**
     * The prefix of generated ids.
     */
    protected $prefixId;

    /**
     * Count the number of ids created.
     */
    protected $count;

    /**
     * Initializes a new object that generate ids for elements.
     * @param string $prefixPart A part of prefix id.
     */
    public function __construct($prefixPart = null) {
        if ($prefixPart === null) {
            $this->prefixId = 'id-hatemile-' . $this->getRandom() . '-';
        } else {
            $this->prefixId = (
                'id-hatemile-' .
                $prefixPart .
                '-' .
                $this->getRandom() .
                '-'
            );
        }
        $this->count = 0;
    }

    /**
     * Returns the random prefix.
     * @return string The random prefix.
     */
    protected function getRandom() {
        return \md5(\uniqid(\rand(), true)) . \md5(\uniqid(\rand(), true));
    }

    /**
     * Generate a id for a element.
     * @param \hatemile\util\html\HTMLDOMElement element The element.
     */
    public function generateId(HTMLDOMElement $element) {
        if (!$element->hasAttribute('id')) {
            $element->setAttribute(
                'id',
                $this->prefixId . ((string) $this->count)
            );
            $this->count++;
        }
    }
}