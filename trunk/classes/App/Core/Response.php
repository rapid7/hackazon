<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 15:11
 */


namespace App\Core;


/**
 * Class Response
 * @inheritdoc
 */
class Response extends \PHPixie\Response
{
    public function setHeader($header, $value = null)
    {
        $set = false;
        foreach ($this->headers as $key => $oldHeader) {
            if (strpos($oldHeader, $header) === 0) {
                if (!$set) {
                    $this->headers[$key] = $header.': '.$value;
                } else {
                    unset($this->headers[$key]);
                }
            }
        }
    }

    public function addHeader($header, $value = null)
    {
        $this->headers[] = $header.($value !== null ? ': '.$value : '');
    }
} 