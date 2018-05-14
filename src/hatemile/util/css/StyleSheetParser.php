<?php
/**
 * Interface StyleSheetParser.
 * 
 * @package hatemile\util\css
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\css;

/**
 * The StyleSheetParser interface contains the methods for access the CSS
 * parser.
 */
interface StyleSheetParser
{

    /**
     * Returns the rules of parser by properties.
     * @param string[] $properties The properties.
     * @return \hatemile\util\css\StyleSheetRule[] The rules.
     */
    public function getRules($properties);
}
