<?php
/**
 * Class PHPCSSParserRule.
 * 
 * @package hatemile\util\css\phpcssparser
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\css\phpcssparser;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'StyleSheetRule.php'
));
require_once join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__),
    'PHPCSSParserDeclaration.php'
));

use \hatemile\util\css\StyleSheetRule;
use \hatemile\util\css\phpcssparser\PHPCSSParserDeclaration;
use \Sabberworm\CSS\RuleSet\DeclarationBlock;

/**
 * The PHPCSSParserRule class is official implementation of StyleSheetRule for
 * Sabberworm PHP CSS Parser.
 */
class PHPCSSParserRule implements StyleSheetRule
{

    /**
     * The Sabberworm PHP CSS declaration block;
     * @var \Sabberworm\CSS\RuleSet\DeclarationBlock
     */
    protected $rule;

    /**
     * Initializes a new object that encapsulate the Sabberworm PHP CSS
     * declaration block.
     * @param \Sabberworm\CSS\RuleSet\DeclarationBlock $declarationBlock The
     * Sabberworm PHP CSS declaration block.
     */
    public function __construct(DeclarationBlock $declarationBlock)
    {
        $this->rule = $declarationBlock;
    }

    public function hasProperty($propertyName)
    {
        return !empty($this->rule->getRules($propertyName));
    }

    public function hasDeclarations()
    {
        return !empty($this->rule->getRules());
    }

    public function getDeclarations($propertyName)
    {
        $declarations = array();
        $nativeDeclarations = $this->rule->getRules($propertyName);
        foreach ($nativeDeclarations as $nativeDeclaration) {
            array_push(
                $declarations,
                new PHPCSSParserDeclaration($nativeDeclaration)
            );
        }
        return $declarations;
    }

    public function getSelector()
    {
        return implode(',', $this->rule->getSelectors());
    }
}
