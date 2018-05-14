<?php
/**
 * Class IDGenerator.
 * 
 * @package hatemile\util
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'html',
    'HTMLDOMElement.php'
));

use \hatemile\util\html\HTMLDOMElement;

/**
 * The IDGenerator class generate ids for
 * {@link \hatemile\util\html\HTMLDOMElement}.
 */
class IDGenerator
{

    /**
     * The prefix of generated ids.
     * @var string
     */
    protected $prefixId;

    /**
     * Count the number of ids created.
     * @var int
     */
    protected $count;

    /**
     * Initializes a new object that generate ids for elements.
     * @param string $prefixPart A part of prefix id.
     */
    public function __construct($prefixPart = null)
    {
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
    protected function getRandom()
    {
        return \md5(\uniqid(\rand(), true)) . \md5(\uniqid(\rand(), true));
    }

    /**
     * Generate a id for a element.
     * @param \hatemile\util\html\HTMLDOMElement $element The element.
     */
    public function generateId(HTMLDOMElement $element)
    {
        if (!$element->hasAttribute('id')) {
            $element->setAttribute(
                'id',
                $this->prefixId . ((string) $this->count)
            );
            $this->count++;
        }
    }
}
