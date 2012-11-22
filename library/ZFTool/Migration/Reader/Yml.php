<?php

/**
 * Read data from Yaml
 *
 * @category Core
 * @package Core_Migration
 * @subpackage Core_Migration_Reader
 * @author V.Leontiev
 * 
 * @version  $Id$
 */
class Core_Migration_Reader_Yml extends Core_Migration_Reader_Abstract
{
    
    /**
     * Load yaml data from the file
     * 
     * @author V.Leontiev
     * @param string $yaml
     * @param array $options
     * @throws Zend_Exception
     */
    public function __construct($yaml, $options = null)
    {
        if (empty($yaml) || !file_exists($yaml)) {
            throw new Zend_Exception('Filename is not set');
        }

        $yaml = file_get_contents($yaml);
        
        $data = self::decode($yaml);
        
        if (null === $data) {
            throw new Zend_Exception("Error parsing YAML data");
        }
        
        parent::__construct($data);
    }
    
    /**
     * Very dumb YAML parser
     *
     * Until we have Zend_Yaml...
     *
     * @param  string $yaml YAML source
     * @return array Decoded data
     */
    public static function decode($yaml)
    {
        $lines = explode("\n", $yaml);
        reset($lines);
        return self::_decodeYaml(0, $lines);
    }

    /**
     * Service function to decode YAML
     *
     * @param  int $currentIndent Current indent level
     * @param  array $lines  YAML lines
     * @return array|string
     */
    protected static function _decodeYaml($currentIndent, &$lines)
    {
        $config   = array();
        $inIndent = false;
        while (list($n, $line) = each($lines)) {
            $lineno = $n + 1;
            
            $line = rtrim(preg_replace("/#.*$/", "", $line));
            if (strlen($line) == 0) {
                continue;
            }

            $indent = strspn($line, " ");

            // line without the spaces
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }

            if ($indent < $currentIndent) {
                // this level is done
                prev($lines);
                return $config;
            }

            if (!$inIndent) {
                $currentIndent = $indent;
                $inIndent      = true;
            }

            if (preg_match("/(\w+):\s*(.*)/", $line, $m)) {
                // key: value
                if (strlen($m[2])) {
                    // simple key: value
                    $value = rtrim(preg_replace("/#.*$/", "", $m[2]));
                    // Check for booleans and constants
                    if (preg_match('/^(t(rue)?|on|y(es)?)$/i', $value)) {
                        $value = true;
                    } elseif (preg_match('/^(f(alse)?|off|n(o)?)$/i', $value)) {
                        $value = false;
                    } else {
                        // test for constants
                        $value = self::_replaceConstants($value);
                    }
                } else {
                    // key: and then values on new lines
                    $value = self::_decodeYaml($currentIndent + 1, $lines);
                    if (is_array($value) && !count($value)) {
                        $value = "";
                    }
                }
                $config[$m[1]] = $value;
            } elseif ($line[0] == "-") {
                // item in the list:
                // - FOO
                if (strlen($line) > 2) {
                    $config[] = substr($line, 2);
                } else {
                    $config[] = self::_decodeYaml($currentIndent + 1, $lines);
                }
            } else {
                throw new Zend_Exception(sprintf(
                    'Error parsing YAML at line %d - unsupported syntax: "%s"',
                    $lineno, $line
                ));
            }
        }
        return $config;
    }

    /**
     * Replace any constants referenced in a string with their values
     *
     * @param  string $value
     * @return string
     */
    protected static function _replaceConstants($value)
    {
        foreach (self::_getConstants() as $constant) {
            if (strstr($value, $constant)) {
                $value = str_replace($constant, constant($constant), $value);
            }
        }
        return $value;
    }

    /**
     * Get (reverse) sorted list of defined constant names
     *
     * @return array
     */
    protected static function _getConstants()
    {
        $constants = array_keys(get_defined_constants());
        rsort($constants, SORT_STRING);
        return $constants;
    }
}

