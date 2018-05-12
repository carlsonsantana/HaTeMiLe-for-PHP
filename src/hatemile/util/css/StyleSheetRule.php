<?php
/**
 * Interface StyleSheetRule.
 * 
 * @package hatemile\util\css
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\css;

/**
 * The StyleSheetRule interface contains the methods for access the CSS rule.
 */
interface StyleSheetRule
{

    /**
     * Returns that the rule has a declaration with the property.
     * @param string $propertyName The name of property.
     * @return bool True if the rule has a declaration with the property or
     * false if the rule not has a declaration with the property.
     */
    public function hasProperty($propertyName);

    /**
     * Returns that the rule has declarations.
     * @return bool True if the rule has the property or false if the rule not
     * has declarations.
     */
    public function hasDeclarations();

    /**
     * Returns the declarations with the property.
     * @param string $propertyName The property.
     * @return \hatemile\util\css\StyleSheetDeclaration[] The declarations with
     * the property.
     */
    public function getDeclarations($propertyName);

    /**
     * Returns the selector of rule.
     * @return string The selector of rule.
     */
    public function getSelector();
}
