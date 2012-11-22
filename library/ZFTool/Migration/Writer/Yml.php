<?php

/**
 * Write data to Yaml
 *
 * @category Core
 * @package Core_Migration
 * @subpackage Core_Migration_Writer
 * @author V.Leontiev
 * 
 * @version  $Id$
 */
class Core_Migration_Writer_Yml
{
    
    public static function encode(array $rows)
    {
        return self::_encodeYaml($rows);
    }

    /**
     * Service function to decode YAML
     *
     * @param  int $currentIndent Current indent level
     * @param  array $lines  YAML lines
     * @return array|string
     */
    protected static function _encodeYaml($rows, $space = 0)
    {
        $yaml = array();
        foreach ($rows as $key => $row) {
            if (empty($row)) {
                continue;
            }
            if (is_numeric($key)) {
                $key = 'row_' . $key;
                $space = 1;
            }
            if (is_array($row)) {
                $yaml[] = str_repeat(' ', $space * 2) . $key . ':';
                $yaml[] = self::_encodeYaml($row, ++$space);
            } else {
                $yaml[] = str_repeat(' ', $space * 2) . $key . ': ' . $row;
            }
        }
        
        return implode("\n", $yaml);
    }
}
