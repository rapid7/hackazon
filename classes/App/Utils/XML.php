<?php
/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov 
 * Date: 22.04.2015
 * Time: 12:27
  */



namespace App\Utils;


class XML 
{
    public static function asXML(array $data, $rootName = 'root')
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><$rootName></$rootName>");
        self::toXML($data, $xml);
        return $xml->asXML();
    }

    /**
     * @param array $data
     * @param \SimpleXMLElement $xml
     */
    public static function toXML(array $data, &$xml)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof \stdClass) {
                $value = (array) $value;
            }
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subNode = $xml->addChild("$key");
                    self::toXML($value, $subNode);

                } else {
                    $subNode = $xml->addChild("item{$key}");
                    self::toXML($value, $subNode);
                }
            } else {
                $xml->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }
}