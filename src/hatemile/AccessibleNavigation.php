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
 * The AccessibleNavigation interface fixes accessibility problems associated
 * with navigation.
 */
interface AccessibleNavigation
{

    /**
     * Provide content skipper for element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    public function fixSkipper(HTMLDOMElement $element);

    /**
     * Provide content skippers.
     */
    public function fixSkippers();

    /**
     * Provide a navigation by heading.
     * @param \hatemile\util\html\HTMLDOMElement $element The heading element.
     */
    public function fixHeading(HTMLDOMElement $element);

    /**
     * Provide a navigation by headings.
     */
    public function fixHeadings();

    /**
     * Provide an alternative way to access the long description of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element with long
     * description.
     */
    public function fixLongDescription(HTMLDOMElement $element);

    /**
     * Provide an alternative way to access the longs descriptions of elements.
     */
    public function fixLongDescriptions();
}
