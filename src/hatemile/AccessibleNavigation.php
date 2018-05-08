<?php
/**
 * Interface AccessibleNavigation.
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
