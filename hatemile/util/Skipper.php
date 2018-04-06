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

namespace hatemile\util;

/**
 * The Skipper class store the selector that will be add a skipper.
 */
class Skipper
{
    
    /**
     * The selector.
     * @var string
     */
    protected $selector;
    
    /**
     * The default text of skipper.
     * @var string
     */
    protected $defaultText;
    
    /**
     * The shortcuts of skipper.
     * @var string
     */
    protected $shortcuts;
    
    /**
     * Inicializes a new object with the values pre-defineds.
     * @param string $selector The selector.
     * @param string $defaultText The default text of skipper.
     * @param string $shortcuts The shortcuts of skipper.
     */
    public function __construct($selector, $defaultText, $shortcuts)
    {
        $this->selector = $selector;
        $this->defaultText = $defaultText;
        if (!empty($shortcuts)) {
            $this->shortcuts = preg_split("/[ \n\t\r]+/", $shortcuts);
        } else {
            $this->shortcuts = array();
        }
    }
    
    /**
     * Returns the selector.
     * @return string The selector.
     */
    public function getSelector()
    {
        return $this->selector;
    }
    
    /**
     * Returns the default text of skipper.
     * @return string The default text of skipper.
     */
    public function getDefaultText()
    {
        return $this->defaultText;
    }
    
    /**
     * Returns the shortcuts of skipper.
     * @return string The shortcuts of skipper.
     */
    public function getShortcuts()
    {
        return array_merge($this->shortcuts);
    }
}
