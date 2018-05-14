<?php
/**
 * Class Configure.
 * 
 * @package hatemile\util
 * @author Carlson Santana Cruz
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @copyright (c) 2018, HaTeMiLe
 */

namespace hatemile\util;

/**
 * The Configure class contains the configuration of HaTeMiLe.
 */
class Configure
{

    /**
     * The parameters of configuration of HaTeMiLe.
     * @var string[]
     */
    protected $parameters;

    /**
     * Initializes a new object that contains the configuration of HaTeMiLe.
     * @param string $fileName The full path of file.
     */
    public function __construct($fileName = null)
    {
        if ($fileName === null) {
            $localesDirectory = join(DIRECTORY_SEPARATOR, array(
                dirname(dirname(dirname(__FILE__))),
                '_locales'
            ));
            $locales = $this->getLocales();
            foreach ($locales as $locale) {
                $fileName = join(DIRECTORY_SEPARATOR, array(
                    $localesDirectory,
                    $locale,
                    'configurations.json'
                ));
                if (file_exists($fileName)) {
                    break;
                } else {
                    $fileName = null;
                }
            }
            if ($fileName === null) {
                $fileName = join(DIRECTORY_SEPARATOR, array(
                    $localesDirectory,
                    'en_US',
                    'configurations.json'
                ));
            }
        }
        $fileContent = file_get_contents($fileName);
        $this->parameters = json_decode($fileContent, true);
    }

    /**
     * Returns the accept languages of user.
     * Adapted from {@link https://bit.ly/2HXPSDH}.
     * @return string[] The locales of user ordened by preference.
     */
    protected function getLocales()
    {
        // Parse the Accept-Language according to:
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
        $langParse = null;
        preg_match_all(
            (
                '/([a-zA-Z_\-]{1,8})(-[a-z]{1,8})*\s*' .
                '(;\s*q\s*=\s*((1(\.0{0,3}))|(0(\.[0-9]{0,3}))))?/i'
            ),
            filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'),
            $langParse
        );

        $langs = $langParse[1]; // M1 - First part of language
        $quals = $langParse[4]; // M4 - Quality Factor

        $numLanguages = count($langs);
        $langArr = array();

        for ($num = 0; $num < $numLanguages; $num++)
        {
            $newLang = str_replace('-', '_', $langs[$num]);
            $newQual = (
                isset($quals[$num]) ?
                (empty($quals[$num]) ? 1.0 : floatval($quals[$num])) :
                0.0
            );

            // Choose whether to upgrade or set the quality factor for the
            // primary language.
            $langArr[$newLang] = (
                (isset($langArr[$newLang])) ?
                max($langArr[$newLang], $newQual) :
                $newQual
            );
        }

        // sort list based on value
        // langArr will now be an array like: array('EN' => 1, 'ES' => 0.5)
        arsort($langArr, SORT_NUMERIC);

        // The languages the client accepts in order of preference.
        return array_keys($langArr);
    }

    /**
     * Returns the parameters of configuration.
     * @return string[] The parameters of configuration.
     */
    public function getParameters()
    {
        return array_merge($this->parameters);
    }

    /**
     * Check that the configuration has an parameter.
     * @param string $parameter The name of parameter.
     * @return bool True if the configuration has the parameter or false if the
     * configuration not has the parameter.
     */
    public function hasParameter($parameter)
    {
        return array_key_exists($parameter, $this->parameters);
    }

    /**
     * Returns the value of a parameter of configuration.
     * @param string $parameter The parameter.
     * @return string The value of the parameter.
     */
    public function getParameter($parameter)
    {
        return $this->parameters[$parameter];
    }
}
