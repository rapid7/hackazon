<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.11.2014
 * Time: 18:15
 */


namespace VulnModule\Config\Condition;


use App\Core\Request;
use App\Helpers\HttpHelper;
use VulnModule\Config\Condition;

class ContentType extends Condition
{
    const IS_ACTIVE = false;

    /**
     * @var array Field names
     */
    protected $types = [];

    function __construct($types = [])
    {
        $this->types = is_array($types) ? $types : [$types];
    }

    public function toArray()
    {
        return [
            'types' => $this->types
        ];
    }

    public function match(Request $request)
    {
        if (in_array($request->method, ['GET', 'DELETE', 'HEAD', 'OPTIONS'])) {
            return true;
        }

        $contentType = HttpHelper::cleanContentType($request->server('CONTENT_TYPE'));
        if (!$contentType) {
            return true;
        }

        return in_array($contentType, $this->types);
    }
}