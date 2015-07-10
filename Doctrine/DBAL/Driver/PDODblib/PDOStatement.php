<?php

namespace Lsw\DoctrinePdoDblib\Doctrine\DBAL\Driver\PDODblib;

class PDOStatement extends \Doctrine\DBAL\Driver\PDOStatement
{
    
    /**
     * {@inheritdoc}
     * 
     * Freetds communicates with the server using UCS-2 (since v7.0).
     * Freetds claims to convert from any given client charset to UCS-2 using iconv. Which should strip or replace unsupported chars. 
     * However in my experience, characters like 👍 (THUMBS UP SIGN, \u1F44D) still end up in Sqlsrv shouting '102 incorrect syntax'.
     * Upon binding a value, this function replaces the unsupported characters.
     */
    public function bindValue($param, $value, $type = \PDO::PARAM_STR)
    {
        if ($type == \PDO::PARAM_STR) {
            $value = static::replaceNonUcs2Chars($value);
        }
        return parent::bindValue($param, $value, $type);
    }

    /**
     * UCS-2 cannot represent Unicode code points outside the BMP. (> 16 bits.)
     * This function replaces those characters in a string with the REPLACEMENT CHARACTER.
     * @param string $val
     * @return string
     */
    public static function replaceNonUcs2Chars($val)
    {
        return is_string($val) ? \preg_replace('/[^\x{0}-x{FFFF}]/u', "�", $val) : $val;
    }
    
    
}
