<?php
/**
 * Class PHPCSSParserDeclaration.
 * 
 * @package hatemile\util\css\phpcssparser
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util\css\phpcssparser;

require_once join(DIRECTORY_SEPARATOR, array(
    dirname(dirname(__FILE__)),
    'StyleSheetDeclaration.php'
));

use \hatemile\util\css\StyleSheetDeclaration;
use \Sabberworm\CSS\Rule\Rule;

class PHPCSSParserDeclaration implements StyleSheetDeclaration
{

    /**
     *
     * @var \Sabberworm\CSS\Rule\Rule
     */
    protected $declaration;

    public function __construct(Rule $param)
    {
        $this->declaration = $param;
    }

    public function getValue()
    {
        return $this->declaration->getValue();
    }

    public function getValues()
    {
        $values = $this->declaration->getValues();
        if (sizeof($values) === 1) {
            return $values[0];
        } else {
            $result = array();
            foreach ($values as $value) {
                foreach ($value as $item) {
                    array_push($result, $item);
                }
            }
            return $result;
        }
    }

    public function getProperty()
    {
        return $this->declaration->getRule();
    }
}
