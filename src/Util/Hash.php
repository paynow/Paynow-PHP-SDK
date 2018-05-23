<?php
namespace Paynow\Util;


class Hash
{
    public static function make(array $values, $integration_key)
    {
        $string = "";
        foreach($values as $key=>$value) {
            if( strtoupper($key) != "HASH" ){
                $string .= $value;
            }
        }
        $string .= $integration_key;

        $hash = hash("sha512", $string);

        return strtoupper($hash);
    }

    public static function verify($values, $key)
    {
        return self::make($values, $key) === $values['hash'];
    }
}
