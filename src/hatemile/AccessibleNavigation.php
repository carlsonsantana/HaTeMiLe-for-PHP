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
 * The AccessibleNavigation interface improve the accessibility of navigation.
 */
interface AccessibleNavigation
{

    /**
     * Provide a content skipper for element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    public function provideNavigationBySkipper(HTMLDOMElement $element);

    /**
     * Provide navigation by content skippers.
     */
    public function provideNavigationByAllSkippers();

    /**
     * Provide navigation by heading.
     * @param \hatemile\util\html\HTMLDOMElement $heading The heading element.
     */
    public function provideNavigationByHeading(HTMLDOMElement $heading);

    /**
     * Provide navigation by headings of page.
     */
    public function provideNavigationByAllHeadings();

    /**
     * Provide an alternative way to access the long description of element.
     * @param \hatemile\util\html\HTMLDOMElement $image The image with long
     * description.
     */
    public function provideNavigationToLongDescription(HTMLDOMElement $image);

    /**
     * Provide an alternative way to access the longs descriptions of all
     * elements of page.
     */
    public function provideNavigationToAllLongDescriptions();
}
