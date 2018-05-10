<?php
/**
 * Interface AccessibleDisplay.
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
 * The AccessibleDisplay interface improve accessibility, showing informations.
 */
interface AccessibleDisplay
{

    /**
     * Display the shortcuts of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element with
     * shortcuts.
     */
    public function displayShortcut(HTMLDOMElement $element);

    /**
     * Display all shortcuts of page.
     */
    public function displayAllShortcuts();

    /**
     * Display the WAI-ARIA role of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    public function displayRole(HTMLDOMElement $element);

    /**
     * Display the WAI-ARIA roles of all elements of page.
     */
    public function displayAllRoles();

    /**
     * Display the headers of each data cell of table.
     * @param \hatemile\util\html\HTMLDOMElement $tableCell The table cell.
     */
    public function displayCellHeader(HTMLDOMElement $tableCell);

    /**
     * Display the headers of each data cell of all tables of page.
     */
    public function displayAllCellHeaders();

    /**
     * Display the WAI-ARIA attributes of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element with
     * WAI-ARIA attributes.
     */
    public function displayWAIARIAStates(HTMLDOMElement $element);

    /**
     * Display the WAI-ARIA attributes of all elements of page.
     */
    public function displayAllWAIARIAStates();

    /**
     * Display the attributes of link.
     * @param \hatemile\util\html\HTMLDOMElement $link The link element.
     */
    public function displayLinkAttributes(HTMLDOMElement $link);

    /**
     * Display the attributes of all links of page.
     */
    public function displayAllLinksAttributes();

    /**
     * Display the title of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element with
     * title.
     */
    public function displayTitle(HTMLDOMElement $element);

    /**
     * Display the titles of all elements of page.
     */
    public function displayAllTitles();

    /**
     * Display the language of element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    public function displayLanguage(HTMLDOMElement $element);

    /**
     * Display the language of all elements of page.
     */
    public function displayAllLanguages();

    /**
     * Display the alternative text of image.
     * @param \hatemile\util\html\HTMLDOMElement $image The image.
     */
    public function displayAlternativeTextImage(HTMLDOMElement $image);

    /**
     * Display the alternative text of all images of page.
     */
    public function displayAllAlternativeTextImages();
}
