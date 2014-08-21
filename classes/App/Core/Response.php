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
    /**
     * @var array|ResponseLink[]
     */
    protected $links = [];

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

    public function addLinkUrl($url, $rel = null)
    {
        $this->addLink($url, $rel);
    }

    public function addLink($url, $rel = null)
    {
        $this->links[] = new ResponseLink($url, $rel);
    }

    /**
     * @inheritdoc
     * @return \PHPixie\Response|Response
     */
    public function send_headers()
    {
        $links = [];
        foreach ($this->links as $link) {
            $links[] = '<'.$link->getUrl().'>' . ($link->getRel() === null ? '' : '; rel="' . $link->getRel() . '"');
        }
        if (count($links)) {
            $this->addHeader('Link', implode(',', $links));
        }

        return parent::send_headers();
    }
} 