<?php
/**
 * Interface StyleSheetDeclaration.
 * 
 * @package hatemile\util\css
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\css;

/**
 * The StyleSheetDeclaration interface contains the methods for access the CSS
 * declaration.
 */
interface StyleSheetDeclaration
{

    /**
     * Returns the value of declaration.
     * @return string The value of declaration.
     */
    public function getValue();

    /**
     * Returns a array with the values of declaration.
     * @return string[] The array with the values of declaration.
     */
    public function getValues();

    /**
     * Returns the property of declaration.
     * @return string The property of declaration.
     */
    public function getProperty();
}
